<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Plugin\Config\Structure;

class Section
{
    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    protected $configGeneral;

    /**
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\General $configGeneral
    ) {
        $this->configGeneral = $configGeneral;
    }

    /**
     * @param \Magento\Config\Model\Config\Structure\Element\Section $section
     * @param bool $result
     * @return string
     */
    public function afterIsVisible(\Magento\Config\Model\Config\Structure\Element\Section $section, $result)
    {
        if ($section->getId() == 'buzzi_publish_events') {
            $result = $this->configGeneral->isEnabled() && !empty($this->configGeneral->getEnabledEvents());
        }
        return $result;
    }
}
