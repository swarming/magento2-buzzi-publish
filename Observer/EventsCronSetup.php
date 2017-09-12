<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Observer;

use Magento\Framework\Event\Observer;

class EventsCronSetup implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    protected $configEvents;

    /**
     * @var \Buzzi\Publish\Helper\EventsCronSetup
     */
    protected $cronSetupHelper;

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    protected $appConfig;

    /**
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Buzzi\Publish\Helper\EventsCronSetup $cronSetupHelper
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $appConfig
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Buzzi\Publish\Helper\EventsCronSetup $cronSetupHelper,
        \Magento\Framework\App\Config\ReinitableConfigInterface $appConfig
    ) {
        $this->configEvents = $configEvents;
        $this->cronSetupHelper = $cronSetupHelper;
        $this->appConfig = $appConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        foreach ($this->configEvents->getAllTypes() as $eventType) {
            $this->cronSetupHelper->setup($eventType);
        }

        $this->appConfig->reinit();
    }
}

