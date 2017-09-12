<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

use Magento\Framework\DataObject;

class Customer
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
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function getCustomerData($customer)
    {
        $payload = [
            'customer_id' => $customer->getId(),
            'email' => $customer->getEmail(),
            'first_name' => $customer->getFirstname(),
            'last_name' => $customer->getLastname(),
            'customer_since' => $customer->getCreatedAt()
        ];

        $transport = new DataObject(['customer' => $customer, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_customer_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getCustomerDataFromOrder($order)
    {
        $payload = [
            'customer_id' => null,
            'email' => $order->getCustomerEmail(),
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
            'customer_since' => $order->getCreatedAt(),
        ];

        $transport = new DataObject(['order' => $order, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_guest_order_customer_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }
}
