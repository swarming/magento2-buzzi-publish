<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

use Magento\Framework\DataObject;

class Customer
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventDispatcher;

    /**
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Event\ManagerInterface $eventDispatcher
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher
    ) {
        $this->dateTime = $dateTime;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return string[]
     */
    public function getCustomerData($customer)
    {
        $payload = [
            'customer_id' => (string)$customer->getId(),
            'email' => (string)$customer->getEmail(),
            'first_name' => (string)$customer->getFirstname(),
            'last_name' => (string)$customer->getLastname(),
            'customer_since' => (string)$this->convertDateTime($customer->getCreatedAt())
        ];

        $transport = new DataObject(['customer' => $customer, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_customer_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return string[]
     */
    public function getCustomerDataFromOrder($order)
    {
        $payload = [
            'email' => (string)$order->getCustomerEmail(),
            'first_name' => (string)$this->getCustomerFirstName($order),
            'last_name' => (string)$this->getCustomerLastName($order)
        ];

        $transport = new DataObject(['order' => $order, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_guest_order_customer_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param string $dateTime
     * @return string
     */
    private function convertDateTime($dateTime)
    {
        return $this->dateTime->gmDate(\DateTime::ATOM, $this->dateTime->strToTime($dateTime));
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getCustomerFirstName($order)
    {
        return $order->getCustomerFirstname() || !$order->getBillingAddress()
            ? $order->getCustomerFirstname()
            : $order->getBillingAddress()->getFirstname();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return string|null
     */
    private function getCustomerLastName($order)
    {
        return $order->getCustomerLastname() || !$order->getBillingAddress()
            ? $order->getCustomerLastname()
            : $order->getBillingAddress()->getLastname();
    }
}
