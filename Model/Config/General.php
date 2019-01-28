<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config;

use Magento\Store\Model\ScopeInterface;

class General extends \Buzzi\Base\Model\Config\General
{
    const XML_PATH_ENABLED_PUBLISH = 'buzzi_base/publish/enabled_publish';
    const XML_PATH_EVENTS = 'buzzi_base/publish/events';

    const XML_PATH_DEFAULT_ACCEPTS_MARKETING = 'buzzi_base/publish/default_accepts_marketing';

    const XML_PATH_RESEND_ENABLE = 'buzzi_base/publish/resend_enable';
    const XML_PATH_RESEND_MAX_TIME = 'buzzi_base/publish/resend_max_time';

    const XML_PATH_ALLOW_COLLECT_GUEST_DATA = 'buzzi_base/publish/allow_collect_guest_data';
    const XML_PATH_MAX_GUEST_EVENTS = 'buzzi_base/publish/max_guest_events';

    const XML_PATH_PRODUCT_IMAGE = 'buzzi_base/publish/product_image';

    const XML_PATH_CUSTOM_GLOBAL_SCHEDULE = 'buzzi_base/publish/custom_global_schedule';
    const XML_PATH_GLOBAL_SCHEDULE = 'buzzi_base/publish/global_schedule';
    const XML_PATH_GLOBAL_START_TIME = 'buzzi_base/publish/global_start_time';
    const XML_PATH_GLOBAL_FREQUENCY = 'buzzi_base/publish/global_frequency';

    const XML_PATH_REMOVE_IMMEDIATELY = 'buzzi_base/publish/remove_immediately';
    const XML_PATH_REMOVING_DELAY = 'buzzi_base/publish/removing_delay';

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return parent::isEnabled($storeId) && $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED_PUBLISH, $this->scopeDefiner->getScope(), $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string[]
     */
    public function getEnabledEvents($storeId = null)
    {
        $eventTypes = $this->scopeConfig->getValue(self::XML_PATH_EVENTS, $this->scopeDefiner->getScope(), $storeId);
        return $eventTypes ? explode(',', $eventTypes) : [];
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function getDefaultAcceptsMarketing($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEFAULT_ACCEPTS_MARKETING, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isAllowCollectGuestData($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ALLOW_COLLECT_GUEST_DATA, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getMaxGuestEvents($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MAX_GUEST_EVENTS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getProductImage($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_IMAGE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isResendEnable($storeId = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_RESEND_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getResendMaxTime($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_RESEND_MAX_TIME, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function isCustomGlobalSchedule()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CUSTOM_GLOBAL_SCHEDULE);
    }

    /**
     * @return string
     */
    public function getGlobalCronSchedule()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GLOBAL_SCHEDULE);
    }

    /**
     * @return int[]
     */
    public function getGlobalCronStartTime()
    {
        $time = $this->scopeConfig->getValue(self::XML_PATH_GLOBAL_START_TIME);
        return $time ? explode(',', $time) : [];
    }

    /**
     * @return string
     */
    public function getGlobalCronFrequency()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GLOBAL_FREQUENCY);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isRemoveImmediately($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_REMOVE_IMMEDIATELY, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getRemovingDelay($storeId = null)
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_REMOVING_DELAY, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
