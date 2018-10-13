<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

namespace Buzzi\Publish\Plugin\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Buzzi\Publish\Helper\AcceptsMarketing;

class AccountManagement
{
    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    private $configGeneral;

    /**
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\General $configGeneral
    ) {
        $this->configGeneral = $configGeneral;
    }

    /**
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string|null $password
     * @param string $redirectUrl
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        $this->processDefaultAcceptsMarketing($customer);
    }

    /**
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $hash
     * @param string $redirectUrl
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreateAccountWithPasswordHash(
        AccountManagementInterface $subject,
        CustomerInterface $customer,
        $hash,
        $redirectUrl = ''
    ) {
        $this->processDefaultAcceptsMarketing($customer);
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void
     */
    private function processDefaultAcceptsMarketing(CustomerInterface $customer)
    {
        $acceptsMarketingAttribute = $customer->getCustomAttribute(AcceptsMarketing::CUSTOMER_ATTR);
        if (!$acceptsMarketingAttribute || $acceptsMarketingAttribute->getValue() === false) {
            $customer->setCustomAttribute(
                AcceptsMarketing::CUSTOMER_ATTR,
                (int)$this->configGeneral->getDefaultAcceptsMarketing($customer->getStoreId())
            );
        }
    }
}
