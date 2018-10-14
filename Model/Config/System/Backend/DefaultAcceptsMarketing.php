<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config\System\Backend;

use Magento\Customer\Model\Customer;
use Buzzi\Publish\Helper\AcceptsMarketing;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

class DefaultAcceptsMarketing extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        $this->updateAcceptsMarketingAttribute($this->getValue());

        return parent::afterSave();
    }

    /**
     * @param string $value
     * @return void
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateAcceptsMarketingAttribute($value)
    {
        $acceptsMarketingAttribute = $this->eavConfig->getAttribute(Customer::ENTITY, AcceptsMarketing::CUSTOMER_ATTR);
        if (!$acceptsMarketingAttribute instanceof \Magento\Eav\Model\Entity\Attribute\AbstractAttribute) {
            throw new LocalizedException(__('Customer attribute %1 is not created.', AcceptsMarketing::CUSTOMER_ATTR));
        }

        $acceptsMarketingAttribute->setDefaultValue($value);

        try {
            $acceptsMarketingAttribute->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Something went wrong during updating %1 customer attribute', AcceptsMarketing::CUSTOMER_ATTR));
        }
    }
}
