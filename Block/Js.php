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
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $data = [
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
        return $this->jsonEncoder->encode($this->getConfig());
    }
}
