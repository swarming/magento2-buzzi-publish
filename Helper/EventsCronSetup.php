<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper;

use Buzzi\Publish\Cron\SubmitEvent;

class EventsCronSetup
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
     * @var \Buzzi\Base\Helper\Config\Backend\CronSetup
     */
    protected $backendCronSetupHelper;

    /**
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Buzzi\Base\Helper\Config\Backend\CronSetup $backendCronSetupHelper
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Buzzi\Base\Helper\Config\Backend\CronSetup $backendCronSetupHelper
    ) {
        $this->configGeneral = $configGeneral;
        $this->configEvents = $configEvents;
        $this->backendCronSetupHelper = $backendCronSetupHelper;
    }

    /**
     * @param string $eventType
     * @return string
     */
    protected function getJobName($eventType)
    {
        return sprintf('buzzi_publish_event_%s_send', $this->configEvents->getEventCode($eventType));
    }

    /**
     * @param string $eventType
     * @return void
     */
    public function setup($eventType)
    {
        $this->backendCronSetupHelper->setupModel($this->getJobName($eventType), $this->getCronModel($eventType));

        if ($this->configEvents->isGlobalSchedule($eventType)) {
            $this->setupGlobal($eventType);
        } else {
            $this->setupIndividual($eventType);
        }
    }

    /**
     * @param string $eventType
     * @return string
     */
    protected function getCronModel($eventType)
    {
        $eventCode = $this->configEvents->getEventCode($eventType);

        return $this->configEvents->getCronModel($eventType)
            ?: SubmitEvent::class . '::submit' . str_replace(' ', '', ucwords(str_replace('_', ' ', $eventCode)));
    }

    /**
     * @param string $eventType
     * @return void
     */
    protected function setupGlobal($eventType)
    {
        if ($this->configGeneral->isCustomGlobalSchedule()) {
            $this->setupGlobalCustomSchedule($eventType);
        } else {
            $this->setupGlobalSchedule($eventType);
        }
    }

    /**
     * @param string $eventType
     * @return void
     */
    protected function setupIndividual($eventType)
    {
        if ($this->configEvents->isCustomSchedule($eventType)) {
            $this->setupCustomSchedule($eventType);
        } else {
            $this->setupSchedule($eventType);
        }
    }

    /**
     * @param string $eventType
     * @return void
     */
    protected function setupGlobalCustomSchedule($eventType)
    {
        $cronSchedule = $this->configGeneral->getGlobalCronSchedule();

        if (!empty($cronSchedule)) {
            $this->backendCronSetupHelper->setupCustomSchedule($this->getJobName($eventType), $cronSchedule);
        }
    }

    /**
     * @param string $eventType
     * @return void
     */
    protected function setupGlobalSchedule($eventType)
    {
        $time = $this->configGeneral->getGlobalCronStartTime();
        $frequency = $this->configGeneral->getGlobalCronFrequency();

        if (!empty($time) && !empty($frequency)) {
            $this->backendCronSetupHelper->setupSchedule($this->getJobName($eventType), $time, $frequency);
        }
    }

    /**
     * @param string $eventType
     * @return void
     */
    protected function setupCustomSchedule($eventType)
    {
        $cronSchedule = $this->configEvents->getCronSchedule($eventType);

        if (!empty($cronSchedule)) {
            $this->backendCronSetupHelper->setupCustomSchedule($this->getJobName($eventType), $cronSchedule);
        }
    }

    /**
     * @param string $eventType
     * @return void
     */
    protected function setupSchedule($eventType)
    {
        $time = $this->configEvents->getCronStartTime($eventType);
        $frequency = $this->configEvents->getCronFrequency($eventType);

        if (!empty($time) && !empty($frequency)) {
            $this->backendCronSetupHelper->setupSchedule($this->getJobName($eventType), $time, $frequency);
        }
    }
}
