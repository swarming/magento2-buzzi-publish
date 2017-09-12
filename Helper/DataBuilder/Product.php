<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

use Magento\Framework\DataObject;
use Magento\Framework\App\Area;

class Product
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventDispatcher;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Event\ManagerInterface $eventDispatcher
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->design = $design;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductData($product)
    {
        $payload = [
            'base_price' => $product->getPrice(),
            'category' => $this->getProductCategories($product),
            'product_sku' => $product->getSku(),
            'product_name' => $product->getName(),
            'product_description' => $product->getShortDescription(),
            'product_image_url' => $this->getFrontendProductImageUrl($product),
            'product_url' => $product->getProductUrl(),
        ];

        $transport = new DataObject(['product' => $product, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_product_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getProductCategories($product)
    {
        $categoryIds = $product->getCategoryIds();
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addIdFilter($categoryIds);
        $categoryCollection->addNameToResult();
        $categoryNames = $categoryCollection->getColumnValues('name');
        return implode(',', $categoryNames);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getFrontendProductImageUrl($product)
    {
        $this->design->setDesignTheme(
            $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND),
            Area::AREA_FRONTEND
        );

        return $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
    }
}
