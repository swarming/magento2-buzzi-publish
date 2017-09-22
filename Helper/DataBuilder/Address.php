<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

use Magento\Framework\DataObject;

class Address
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventDispatcher;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventDispatcher
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Magento\Customer\Model\Address|\Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $address
     * @return array
     */
    public function getAddressData($address)
    {
        $payload = [
            'company' => $address->getCompany(),
            'name' => $address->getName(),
            'street' => implode(' ', (array)$address->getStreet()),
            'state' => $address->getRegionCode(),
            'city' => $address->getCity(),
            'zip' => $address->getPostcode(),
            'country' => $address->getCountryId(),
            'phone' => $address->getTelephone(),
        ];

        $transport = new DataObject(['address' => $address, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_address_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param \Magento\Customer\Model\Address|\Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address $address
     * @return array|string
     */
    protected function validateAndRenderAddress($address)
    {
        return $address && $address->getName() ? $this->getAddressData($address) : '';
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return array|string
     */
    public function getBillingAddresses($customer)
    {
        return $this->validateAndRenderAddress($customer->getPrimaryBillingAddress());
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return array|string
     */
    public function getShippingAddresses($customer)
    {
        return $this->validateAndRenderAddress($customer->getPrimaryShippingAddress());
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array|string
     */
    public function getBillingAddressesFromQuote($quote)
    {
        return $this->validateAndRenderAddress($quote->getBillingAddress());
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array|string
     */
    public function getShippingAddressesFromQuote($quote)
    {
        return $this->validateAndRenderAddress($quote->getShippingAddress());
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array|string
     */
    public function getBillingAddressesFromOrder($order)
    {
        return $this->validateAndRenderAddress($order->getBillingAddress());
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array|string
     */
    public function getShippingAddressesFromOrder($order)
    {
        return $this->validateAndRenderAddress($order->getShippingAddress());
    }
}
