<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Helper\DataBuilder;

use Magento\Framework\DataObject;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Buzzi\Publish\Model\Config\General as GeneralConfig;

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
     * @var \Buzzi\Publish\Model\Config\General
     */
    private $configGeneral;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Event\ManagerInterface $eventDispatcher
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Event\ManagerInterface $eventDispatcher,
        \Buzzi\Publish\Model\Config\General $configGeneral = null
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->design = $design;
        $this->eventDispatcher = $eventDispatcher;
        $this->configGeneral = $configGeneral ?: ObjectManager::getInstance()->get(GeneralConfig::class);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int|null $storeId
     * @return array
     */
    public function getProductData($product, $storeId = null)
    {
        $payload = [
            'base_price' => (string)$product->getPrice(),
            'category' => $this->getProductCategories($product),
            'product_sku' => (string)$product->getSku(),
            'product_name' => (string)$product->getName(),
            'product_description' => (string)$product->getShortDescription(),
            'product_image_url' => (string)$this->getFrontendProductImageUrl($product, $storeId),
            'product_url' => (string)$product->getProductUrl(),
        ];

        $transport = new DataObject(['product' => $product, 'payload' => $payload]);
        $this->eventDispatcher->dispatch('buzzi_publish_product_build_after', ['transport' => $transport]);

        return (array)$transport->getData('payload');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string[]
     */
    protected function getProductCategories($product)
    {
        $categoryIds = $product->getCategoryIds();
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addIdFilter($categoryIds);
        $categoryCollection->addNameToResult();
        return $categoryCollection->getColumnValues('name');
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int|null $storeId
     * @return string
     */
    protected function getFrontendProductImageUrl($product, $storeId = null)
    {
        if ($this->design->getArea() !== Area::AREA_FRONTEND) {
            $this->setFrontendTheme($storeId);
        }

        return $this->imageHelper->init($product, $this->configGeneral->getProductImage($storeId))->getUrl();
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    private function setFrontendTheme($storeId = null)
    {
        $params = $storeId ? ['store' => $storeId] : [];

        $this->design->setDesignTheme(
            $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND, $params),
            Area::AREA_FRONTEND
        );
    }
}
