<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Service;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Buzzi\Publish\Api\Data\SubmissionInterface;

class SubmissionRepository implements \Buzzi\Publish\Api\SubmissionRepositoryInterface
{
    /**
     * @var \Buzzi\Publish\Api\Data\SubmissionInterfaceFactory
     */
    protected $submissionFactory;

    /**
     * @var \Buzzi\Publish\Model\ResourceModel\Submission
     */
    protected $submissionResource;

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterfaceFactory $submissionFactory
     * @param \Buzzi\Publish\Model\ResourceModel\Submission $submissionResource
     */
    public function __construct(
        \Buzzi\Publish\Api\Data\SubmissionInterfaceFactory $submissionFactory,
        \Buzzi\Publish\Model\ResourceModel\Submission $submissionResource
    ) {
        $this->submissionFactory = $submissionFactory;
        $this->submissionResource = $submissionResource;
    }

    /**
     * @param mixed[] $data
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function getNew(array $data = [])
    {
        return $this->submissionFactory->create($data);
    }

    /**
     * @param int $submissionId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($submissionId)
    {
        $submission = $this->getNew();
        $this->submissionResource->load($submission, $submissionId);
        if (!$submission->getSubmissionId()) {
            throw new NoSuchEntityException(__('Submission with id "%1" does not exist.', $submissionId));
        }
        return $submission;
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SubmissionInterface $submission)
    {
        try {
            $this->submissionResource->save($submission);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save submission: %1', $e->getMessage()));
        }
        return $submission;
    }

    /**
     * @param \Buzzi\Publish\Api\Data\SubmissionInterface $submission
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SubmissionInterface $submission)
    {
        try {
            $this->submissionResource->delete($submission);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete submission: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $submissionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($submissionId)
    {
        return $this->delete($this->getById($submissionId));
    }
}
