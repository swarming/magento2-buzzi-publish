<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper;

class Customer
{
    const ATTR_EXCEPTS_MARKETING = 'excepts_marketing';

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @return bool
     */
    public function isCurrentExceptsMarketing()
    {
        return $this->isExceptsMarketing($this->customerSession->getCustomer()->getDataModel());
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    public function isExceptsMarketing($customer)
    {
        $exceptsMarketingAttribute = $customer->getCustomAttribute(self::ATTR_EXCEPTS_MARKETING);
        return $exceptsMarketingAttribute ? $exceptsMarketingAttribute->getValue() : false;
    }
}
