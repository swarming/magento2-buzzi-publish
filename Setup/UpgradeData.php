<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

namespace Buzzi\Publish\Setup;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Model\Customer;
use Buzzi\Publish\Helper\ExceptsMarketing;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->appState = $appState;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addExceptsMarketingCustomerAttribute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @return void
     */
    private function addExceptsMarketingCustomerAttribute($setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            ExceptsMarketing::CUSTOMER_ATTR,
            [
                'label'      => 'Excepts Marketing',
                'type'       => 'int',
                'input'      => 'boolean',
                'source'     => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'system'     => false,
                'visible'    => true,
                'required'   => false,
                'default'    => '1',
                'sort_order' => '120',
                'position'   => '120',
            ]
        );

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, ExceptsMarketing::CUSTOMER_ATTR);
        $attribute->addData(['used_in_forms' => ['adminhtml_customer', 'customer_account_create']]);
        $attribute->save();

        $this->eavConfig->clear();
    }
}
