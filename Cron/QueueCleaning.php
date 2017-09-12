<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Cron;

class QueueCleaning
{
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    protected $configGeneral;

    /**
     * @var \Buzzi\Publish\Api\QueueInterface
     */
    protected $queue;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Buzzi\Publish\Api\QueueInterface $queue
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Buzzi\Publish\Api\QueueInterface $queue,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->storeRepository = $storeRepository;
        $this->configGeneral = $configGeneral;
        $this->queue = $queue;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute()
    {
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() == 0) {
                continue;
            }

            $delay = $this->configGeneral->isRemoveImmediately($store->getId())
                ? 0
                : $this->configGeneral->getRemovingDelay($store->getId());

            try {
                $this->queue->deleteDone($delay, $store->getId());
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }
}
