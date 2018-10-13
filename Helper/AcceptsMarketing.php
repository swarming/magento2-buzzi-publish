<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;

class AcceptsMarketing
{
    const CUSTOMER_ATTR = 'accepts_marketing';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    private $configEvents;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->customerSession = $customerSession;
        $this->configEvents = $configEvents;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param string $eventType
     * @param int|null $storeId
     * @param \Magento\Customer\Api\Data\CustomerInterface|null $customer
     * @return bool
     */
    public function isAccepts($eventType, $storeId = null, CustomerInterface $customer = null)
    {
        $isAccepts = true;

        if ($this->configEvents->isSetFlag($eventType, 'respect_accepts_marketing', $storeId)) {
            $customer = $customer ?? $this->customerSession->getCustomer()->getDataModel();
            $isAccepts = $this->isAcceptsMarketing($customer);
        }

        return $isAccepts;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    private function isAcceptsMarketing($customer)
    {
        $acceptsMarketingAttribute = $customer->getCustomAttribute(self::CUSTOMER_ATTR);
        return $acceptsMarketingAttribute
            ? (bool)$acceptsMarketingAttribute->getValue()
            : (bool)$this->eavConfig->getAttribute(Customer::ENTITY, self::CUSTOMER_ATTR)->getDefaultValue();
    }
}
