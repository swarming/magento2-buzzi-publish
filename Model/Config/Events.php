<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config;

use Magento\Store\Model\ScopeInterface;

class Events
{
    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    protected $configGeneral;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var mixed[]
     */
    protected $eventsConfigData;

    /**
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Buzzi\Publish\Model\Config\Structure\Event $eventsStructure
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Buzzi\Publish\Model\Config\Structure\Event $eventsStructure
    ) {
        $this->configGeneral = $configGeneral;
        $this->eventsConfigData = $eventsStructure->get();
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string[]
     */
    public function getAllTypes()
    {
        $allTypes = array_keys($this->eventsConfigData);
        sort($allTypes);
        return $allTypes;
    }

    /**
     * @param string $type
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEventEnabled($type, $storeId = null)
    {
        return $this->configGeneral->isEnabled($storeId)
            && isset($this->eventsConfigData[$type])
            && in_array($type, $this->configGeneral->getEnabledEvents($storeId));
    }

    /**
     * @param string $type
     * @return string
     */
    public function getEventCode($type)
    {
        return $this->getEventConfigValue($type, 'code');
    }

    /**
     * @param string $type
     * @return string
     */
    public function getEventLabel($type)
    {
        return $this->getEventConfigValue($type, 'label') ?: $type;
    }

    /**
     * @param string $type
     * @param int|string|null $storeId
     * @return bool
     */
    public function isCron($type, $storeId = null)
    {
        return $this->isSetFlag($type, 'is_cron', $storeId) || $this->isCronOnly($type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isCronOnly($type)
    {
        return (bool)$this->getEventConfigValue($type, 'cron_only');
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isGlobalSchedule($type)
    {
        return (bool)$this->getValue($type, 'global_schedule');
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isCustomSchedule($type)
    {
        return (bool)$this->getValue($type, 'custom_schedule');
    }

    /**
     * @param string $type
     * @return string|null
     */
    public function getCronModel($type)
    {
        return $this->getEventConfigValue($type, 'cron_model');
    }

    /**
     * @param string $type
     * @return string
     */
    public function getCronSchedule($type)
    {
        return $this->getValue($type, 'cron_schedule');
    }

    /**
     * @param string $type
     * @return int[]
     */
    public function getCronStartTime($type)
    {
        $time = $this->getValue($type, 'cron_start_time');
        return $time ? explode(',', $time) : [];
    }

    /**
     * @param string $type
     * @return string
     */
    public function getCronFrequency($type)
    {
        return $this->getValue($type, 'cron_frequency');
    }

    /**
     * @param string $type
     * @param string $field
     * @param int|string|null $storeId
     * @return string
     */
    public function getValue($type, $field, $storeId = null)
    {
        return $this->scopeConfig->getValue("buzzi_publish_events/{$this->getEventCode($type)}/{$field}", ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param string $type
     * @param string $field
     * @param int|string|null $storeId
     * @return string
     */
    public function isSetFlag($type, $field, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag("buzzi_publish_events/{$this->getEventCode($type)}/{$field}", ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param string $type
     * @param string $code
     * @return mixed|null
     * @throws \DomainException
     */
    protected function getEventConfigValue($type, $code)
    {
        if (!isset($this->eventsConfigData[$type])) {
            throw new \DomainException("{$type} publish event is not supported.");
        }

        return isset($this->eventsConfigData[$type][$code])
            ? $this->eventsConfigData[$type][$code]
            : null;
    }
}
