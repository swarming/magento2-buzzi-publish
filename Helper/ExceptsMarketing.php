<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper;

use Magento\Customer\Api\Data\CustomerInterface;

class ExceptsMarketing
{
    const CUSTOMER_ATTR = 'excepts_marketing';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    private $configEvents;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Buzzi\Publish\Model\Config\Events $configEvents
    ) {
        $this->customerSession = $customerSession;
        $this->configEvents = $configEvents;
    }

    /**
     * @param string $eventType
     * @param int|null $storeId
     * @param \Magento\Customer\Api\Data\CustomerInterface|null $customer
     * @return bool
     */
    public function isExcepts($eventType, $storeId = null, CustomerInterface $customer = null)
    {
        $isExcepts = true;

        if ($this->configEvents->isSetFlag($eventType, 'respect_accepts_marketing', $storeId)) {
            $customer = $customer ?? $this->customerSession->getCustomer()->getDataModel();
            $isExcepts = $this->isExceptsMarketing($customer);
        }

        return $isExcepts;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    private function isExceptsMarketing($customer)
    {
        $exceptsMarketingAttribute = $customer->getCustomAttribute(self::CUSTOMER_ATTR);
        return $exceptsMarketingAttribute ? $exceptsMarketingAttribute->getValue() : false;
    }
}
