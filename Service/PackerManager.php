<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Service;

use Magento\Framework\Exception\NoSuchEntityException;

class PackerManager implements \Buzzi\Publish\Api\PackerManagerInterface
{
    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    private $configEvents;

    /**
     * @var \Magento\Store\Api\StoreResolverInterface
     */
    private $storeResolver;

    /**
     * @var \Buzzi\Publish\Helper\ExceptsMarketing
     */
    private $exceptsMarketingHelper;

    /**
     * @var \Buzzi\Publish\Api\QueueInterface
     */
    private $queue;

    /**
     * @var \Buzzi\Publish\Service\PackerRepository
     */
    private $packerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Magento\Store\Api\StoreResolverInterface $storeResolver
     * @param \Buzzi\Publish\Helper\ExceptsMarketing $exceptsMarketingHelper
     * @param \Buzzi\Publish\Api\QueueInterface $queue
     * @param \Buzzi\Publish\Service\PackerRepository $packerRepository
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Magento\Store\Api\StoreResolverInterface $storeResolver,
        \Buzzi\Publish\Helper\ExceptsMarketing $exceptsMarketingHelper,
        \Buzzi\Publish\Api\QueueInterface $queue,
        \Buzzi\Publish\Service\PackerRepository $packerRepository,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configEvents = $configEvents;
        $this->storeResolver = $storeResolver;
        $this->exceptsMarketingHelper = $exceptsMarketingHelper;
        $this->queue = $queue;
        $this->packerRepository = $packerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @param string $eventType
     * @param mixed $inputData
     * @param int|null $customerId
     * @param string|null $guestEmail
     * @return void
     */
    public function deliver($eventType, $inputData, $customerId = null, $guestEmail = null)
    {
        if (!$this->configEvents->isEventEnabled($eventType)) {
            return;
        }

        try {
            $customer = $this->getCustomer($customerId, $guestEmail);

            if ($customer && !$this->exceptsMarketingHelper->isExcepts($eventType, null, $customer->getDataModel())) {
                return;
            }

            $packer = $this->packerRepository->getPacker($eventType);
            $payload = $packer->pack($inputData, $customer, $guestEmail);

            if ($payload) {
                $payload = $this->updateCreatingTime($inputData, $payload);
                $this->submitPayload($eventType, $payload);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * @param string $eventType
     * @param array $payload
     * @return void
     */
    private function submitPayload($eventType, $payload)
    {
        $storeId = $this->storeResolver->getCurrentStoreId();
        if ($this->configEvents->isCron($eventType, $storeId)) {
            $this->queue->add($eventType, $payload, $storeId);
        } else {
            $this->queue->send($eventType, $payload, $storeId);
        }
    }

    /**
     * @param array $inputData
     * @param array $payload
     * @return array
     */
    private function updateCreatingTime($inputData, $payload)
    {
        if (!empty($inputData['creatingTime'])) {
            $payload['timestamp'] = $this->dateTime->gmDate(\DateTime::ATOM, $inputData['creatingTime']);
        }
        return $payload;
    }

    /**
     * @param int|null $customerId
     * @param string|null $customerEmail
     * @return \Magento\Customer\Model\Customer|null
     */
    private function getCustomer($customerId = null, $customerEmail = null)
    {
        $customer = $customerId ? $this->customerRegistry->retrieve($customerId) : null;
        if (!$customer && $customerEmail) {
            $customer = $this->getCustomerByEmail($customerEmail);
        }
        return $customer;
    }

    /**
     * @param string $customerEmail
     * @return \Magento\Customer\Model\Customer|null
     */
    private function getCustomerByEmail($customerEmail)
    {
        try {
            $customer = $this->customerRegistry->retrieveByEmail($customerEmail);
        } catch (NoSuchEntityException $e) {
            $customer = null;
            // Do nothing, Skip
        }
        return $customer;
    }
}
