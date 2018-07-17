<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Api;

interface PackerInterface
{
    /**
     * @param array $inputData
     * @param \Magento\Customer\Model\Customer|null $customer
     * @param string|null $customerEmail
     * @return array|null
     */
    public function pack(array $inputData, $customer = null, $customerEmail = null);
}
