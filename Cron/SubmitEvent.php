<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Cron;

class SubmitEvent
{
    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    protected $configEvents;

    /**
     * @var \Buzzi\Publish\Api\QueueInterface
     */
    protected $queue;

    /**
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepository
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Buzzi\Publish\Api\QueueInterface $queue
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Buzzi\Publish\Api\QueueInterface $queue
    ) {
        $this->storeRepository = $storeRepository;
        $this->configEvents = $configEvents;
        $this->queue = $queue;
    }

    /**
     * @param string $eventType
     * @return void
     */
    public function submit($eventType)
    {
        foreach ($this->storeRepository->getList() as $store) {
            if ($store->getId() == 0
                || !$this->configEvents->isEventEnabled($eventType, $store->getId())
                || !$this->configEvents->isCron($eventType, $store->getId())
            ) {
                continue;
            }

            $this->queue->sendByType($eventType, $store->getId());
        }
    }

    /**
     * @param string $method
     * @param array $args
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 6) != 'submit') {
            throw new \BadMethodCallException(sprintf('Method "%s" does not exist.', $method));
        }

        $eventCode = $this->underscore(substr($method, 6));
        $eventType = $this->getEventType($eventCode);
        $this->submit($eventType);
    }

    /**
     * @param string $eventCode
     * @return string
     */
    protected function getEventType($eventCode)
    {
        foreach ($this->configEvents->getAllTypes() as $eventType) {
            if ($this->configEvents->getEventCode($eventType) == $eventCode) {
                return $eventType;
            }
        }

        throw new \BadMethodCallException(sprintf('Invalid "%s" event code.', $eventCode));
    }

    /**
     * @param string $name
     * @return string
     */
    protected function underscore($name)
    {
        return strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', '_$1', $name), '_'));
    }
}
