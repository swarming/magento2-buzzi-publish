<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config\System\Source;

class ProductImageType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Main Page Image'),
                'value' => 'product_page_main_image',
            ],
            [
                'label' => __('Base Image'),
                'value' => 'product_base_image',
            ],
            [
                'label' => __('Small Image'),
                'value' => 'product_small_image',
            ],
            [
                'label' => __('Thumbnail'),
                'value' => 'product_thumbnail_image',
            ]
        ];
    }
}
