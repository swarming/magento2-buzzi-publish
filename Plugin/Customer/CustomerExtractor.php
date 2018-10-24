<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

namespace Buzzi\Publish\Plugin\Customer;

use Buzzi\Publish\Helper\AcceptsMarketing;

class CustomerExtractor
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
     * @param \Magento\Customer\Model\CustomerExtractor $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $result
     * @param string $formCode
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $attributeValues
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function afterExtract(
        \Magento\Customer\Model\CustomerExtractor $subject,
        $result,
        $formCode,
        \Magento\Framework\App\RequestInterface $request,
        array $attributeValues = []
    ) {
        if ('customer_account_create' === $formCode && $request->getParam('accepts_marketing') === null) {
            $this->updateDefaultValue($result);
        }

        return $result;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return void
     */
    private function updateDefaultValue(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $customer->setCustomAttribute(AcceptsMarketing::CUSTOMER_ATTR, $this->configGeneral->getDefaultAcceptsMarketing());
    }
}
