<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Service;

use Magento\Framework\DataObject;
use Buzzi\Publish\Api\Data\SubmissionInterface;

class Queue implements \Buzzi\Publish\Api\QueueInterface
{
    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    protected $configGeneral;

    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    protected $configEvents;

    /**
     * @var \Buzzi\Publish\Api\PlatformInterface
     */
    protected $platform;

    /**
     * @var \Buzzi\Publish\Model\Submission\PayloadPacker
     */
    protected $payloadPacker;

    /**
     * @var \Buzzi\Publish\Api\SubmissionRepositoryInterface
     */
    protected $submissionRepository;

    /**
     * @var \Buzzi\Publish\Model\ResourceModel\Submission\CollectionFactory
     */
    protected $submissionCollectionFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Buzzi\Publish\Api\PlatformInterface $platform
     * @param \Buzzi\Publish\Model\Submission\PayloadPacker $payloadPacker
     * @param \Buzzi\Publish\Api\SubmissionRepositoryInterface $submissionRepository
     * @param \Buzzi\Publish\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventDispatcher
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Buzzi\Publish\Api\PlatformInterface $platform,
        \Buzzi\Publish\Model\Submission\PayloadPacker $payloadPacker,
        \Buzzi\Publish\Api\SubmissionRepositoryInterface $submissionRepository,
        \Buzzi\Publish\Model\ResourceModel\Submission\CollectionFactory $submissionCollectionFactory,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configGeneral = $configGeneral;
        $this->configEvents = $configEvents;
        $this->platform = $platform;
        $this->payloadPacker = $payloadPacker;
        $this->submissionRepository = $submissionRepository;
        $this->submissionCollectionFactory = $submissionCollectionFactory;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @param string $eventType
     * @param mixed[] $payload
     * @param int|string $storeId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function add($eventType, array $payload, $storeId)
    {
        $transport = new DataObject(['event_type' => $eventType, 'store_id' => $storeId, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_add_to_queue_before', ['transport' => $transport]);
        $payload = (array)$transport->getData('payload');

        $submission = $this->submissionRepository->getNew();
        $submission->setStoreId($storeId);
        $submission->setEventType($eventType);
        $submission->setStatus(SubmissionInterface::STATUS_PENDING);
        $this->payloadPacker->packPayload($submission, $payload);
        $this->submissionRepository->save($submission);

        return $submission;
    }

    /**
     * @param string $eventType
     * @param mixed[] $payload
     * @param int|string $storeId
     * @return bool
     */
    public function send($eventType, array $payload, $storeId)
    {
        $submission = $this->add($eventType, $payload, $storeId);
        return $this->submit($submission);
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return bool
     */
    protected function submit($submission)
    {
        if (SubmissionInterface::STATUS_DONE == $submission->getStatus()) {
            throw new \InvalidArgumentException(sprintf('Submission with %s ID is already sent.', $submission->getSubmissionId()));
        }

        try {
            $eventId = $this->platform->send($submission->getEventType(), $this->payloadPacker->unpackPayload($submission), $submission->getStoreId());
            $errorMessage = null;
        } catch (\Exception $e) {
            $eventId = null;
            $errorMessage = $e->getMessage();
        }

        if ($eventId && $this->configGeneral->isRemoveImmediately($submission->getStoreId())) {
            $this->deleteSubmission($submission);
        } else {
            $this->updateSubmission($submission, $eventId, $errorMessage);
        }

        return (bool)$eventId;
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @param string|bool $eventId
     * @param string|null $errorMessage
     * @return void
     */
    protected function updateSubmission($submission, $eventId, $errorMessage)
    {
        if ($eventId) {
            $submission->setEventId($eventId);
            $submission->setStatus(SubmissionInterface::STATUS_DONE);
            $submission->setErrorMessage('');
        } else {
            $submission->setStatus(SubmissionInterface::STATUS_FAIL);
            $submission->setErrorMessage($errorMessage);
        }

        $count = $submission->getCounter();
        $submission->setCounter(++$count);

        $this->submissionRepository->save($submission);
    }

    /**
     * @param \Buzzi\Publish\Model\ResourceModel\Submission\Collection $submissions
     * @return int
     */
    protected function submitSubmissions($submissions)
    {
        $counter = 0;
        /** @var \Buzzi\Publish\Api\Data\SubmissionInterface $submission */
        foreach ($submissions as $submission) {
            try {
                $counter += $this->submit($submission) ? 1 : 0;
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
        return $counter;
    }

    /**
     * @param int[] $submissionIds
     * @return int
     */
    public function sendByIds(array $submissionIds)
    {
        $submissions = $this->submissionCollectionFactory->create();
        $submissions->filterNotDone();
        if ($submissionIds) {
            $submissions->filterSubmissionIds($submissionIds);
        }

        return $this->submitSubmissions($submissions);
    }

    /**
     * @param string $eventType
     * @param int|string $storeId
     * @return int
     */
    public function sendByType($eventType, $storeId)
    {
        $submissions = $this->submissionCollectionFactory->create();
        $submissions->filterEventType($eventType);
        $submissions->filterPending();
        if ($storeId) {
            $submissions->filterStore($storeId);
        }

        return $this->submitSubmissions($submissions);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function resendFailed($storeId = null)
    {
        $submissions = $this->submissionCollectionFactory->create();
        $submissions->filterFailed($this->configGeneral->getResendMaxTime($storeId));
        if ($storeId) {
            $submissions->filterStore($storeId);
        }

        return $this->submitSubmissions($submissions);
    }

    /**
     * @param int $delay
     * @param int|null $storeId
     * @return void
     */
    public function deleteDone($delay, $storeId = null)
    {
        $submissions = $this->submissionCollectionFactory->create();
        $submissions->filterDone();
        $submissions->filterSubmissionTime($delay);
        if ($storeId) {
            $submissions->filterStore($storeId);
        }

        /** @var \Buzzi\Publish\Api\Data\SubmissionInterface $submission */
        foreach ($submissions as $submission) {
            $this->deleteSubmission($submission);
        };
    }

    /**
     * @param int[] $submissionIds
     * @return void
     */
    public function deleteByIds(array $submissionIds)
    {
        $submissions = $this->submissionCollectionFactory->create();
        if ($submissionIds) {
            $submissions->filterSubmissionIds($submissionIds);
        }

        /** @var \Buzzi\Publish\Api\Data\SubmissionInterface $submission */
        foreach ($submissions as $submission) {
            $this->deleteSubmission($submission);
        };
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return void
     */
    protected function deleteSubmission($submission)
    {
        $this->payloadPacker->cleanPayload($submission);
        $this->submissionRepository->delete($submission);
    }
}
