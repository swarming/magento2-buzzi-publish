<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Block;

class Js extends \Magento\Backend\Block\Template
{
    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Buzzi\Publish\Helper\Customer
     */
    private $customerHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializerJson;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Buzzi\Publish\Helper\Customer $customerHelper
     * @param \Magento\Framework\Serialize\Serializer\Json $serializerJson
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Buzzi\Publish\Helper\Customer $customerHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializerJson,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->customerHelper = $customerHelper;
        $this->serializerJson = $serializerJson;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $data = [
            'excepts_marketing' => $this->customerHelper->isCurrentExceptsMarketing(),
            'collect_guest_data' => $this->configGeneral->isAllowCollectGuestData(),
            'max_guest_events' => $this->configGeneral->getMaxGuestEvents()
        ];
        return $data;
    }

    /**
     * @return string
     */
    public function getConfigJson()
    {
        return $this->serializerJson->serialize($this->getConfig());
    }
}
