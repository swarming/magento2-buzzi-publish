<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config\System\Backend;

class CronResend extends \Magento\Framework\App\Config\Value
{
    const JOB_NAME = 'buzzi_publish_resend';

    const CRON_CUSTOM_SCHEDULE = 'groups/publish/fields/resend_custom_schedule/value';
    const CRON_SCHEDULE = 'groups/publish/fields/resend_schedule/value';
    const CRON_SCHEDULE_TIME = 'groups/publish/fields/resend_start_time/value';
    const CRON_SCHEDULE_FREQUENCY = 'groups/publish/fields/resend_frequency/value';

    /**
     * @var \Buzzi\Base\Helper\Config\Backend\CronSetup
     */
    protected $backendCronSetupHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Buzzi\Base\Helper\Config\Backend\CronSetup $backendCronSetupHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Buzzi\Base\Helper\Config\Backend\CronSetup $backendCronSetupHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->backendCronSetupHelper = $backendCronSetupHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        $isCustomSchedule = $this->getData(self::CRON_CUSTOM_SCHEDULE);
        if ($isCustomSchedule) {
            $this->setCustomSchedule();
        } else {
            $this->setupSchedule();
        }

        return parent::afterSave();
    }

    /**
     * @return void
     */
    protected function setupSchedule()
    {
        $time = $this->getData(self::CRON_SCHEDULE_TIME);
        $frequency = $this->getData(self::CRON_SCHEDULE_FREQUENCY);

        if (!empty($time) && !empty($frequency)) {
            $this->backendCronSetupHelper->setupSchedule(self::JOB_NAME, $time, $frequency);
        }
    }

    /**
     * @return void
     */
    protected function setCustomSchedule()
    {
        $schedule = $this->getData(self::CRON_SCHEDULE);
        if (!empty($schedule)) {
            $this->backendCronSetupHelper->setupCustomSchedule(self::JOB_NAME, $schedule);
        }
    }
}
