<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

use Magento\Framework\DataObject;

class Cart
{
    /**
     * @var \Buzzi\Publish\Helper\DataBuilder\Product
     */
    protected $dataBuilderProduct;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventDispatcher;

    /**
     * @param \Buzzi\Publish\Helper\DataBuilder\Product $dataBuilderProduct
     * @param \Magento\Framework\Event\ManagerInterface $eventDispatcher
     */
    public function __construct(
        \Buzzi\Publish\Helper\DataBuilder\Product $dataBuilderProduct,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher
    ) {
        $this->dataBuilderProduct = $dataBuilderProduct;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Sales\Model\Order|null $order
     * @return array
     */
    public function getCartData($quote, $order = null)
    {
        $shippingAddress = $quote->getShippingAddress();
        $totals = $quote->getTotals();
        $payload = [
            'order_id' => $order ? $order->getIncrementId() : '',
            'quantity' => $quote->getItemsQty(),
            'order_promo' => $quote->getCouponCode(),
            'currency' => $quote->getQuoteCurrencyCode(),
            'order_subtotal' => $this->getTotalValue($totals, 'subtotal'),
            'order_shipping' => $this->getTotalValue($totals, 'shipping'),
            'order_tax' => $this->getTotalValue($totals, 'tax'),
            'order_discount' => $this->getTotalValue($totals, 'discount'),
            'order_total' => $this->getTotalValue($totals, 'grand_total'),
            'shipping_method' => $shippingAddress->getId() ? $shippingAddress->getShippingMethod() : '',
            'shipping_carrier' => $shippingAddress->getId() ? $shippingAddress->getShippingDescription() : ''
        ];

        $transport = new DataObject(['quote' => $quote, 'order' => $order, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_cart_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param array $totals
     * @param string $totalType
     * @return string
     */
    protected function getTotalValue($totals, $totalType)
    {
        return !empty($totals[$totalType]) ? $totals[$totalType]->getValue() : '';
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function getCartItemsData($quote)
    {
        $payload = [];

        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            $itemPayload = $this->dataBuilderProduct->getProductData($item->getProduct());
            $itemPayload['base_price'] = $item->getPrice();
            $itemPayload['product_sku'] = $item->getSku();
            $itemPayload['quantity'] = $item->getQty();

            $transport = new DataObject(['quote_item' => $item, 'product' => $item->getProduct(), 'payload' => $itemPayload]);
            $this->eventDispatcher->dispatch('buzzi_publish_cart_item_build_after', ['transport' => $transport]);

            $payload[] = (array)$transport->getData('payload');
        }

        $transport = new DataObject(['quote' => $quote, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_cart_items_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }
}
