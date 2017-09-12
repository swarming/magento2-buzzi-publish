<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config\System\Source;

class EventType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    protected $configEvents;

    /**
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\Events $configEvents
    ) {
        $this->configEvents = $configEvents;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->configEvents->getAllTypes() as $eventType) {
            $options[] = [
                'label' => $this->configEvents->getEventLabel($eventType),
                'value' => $eventType,
            ];
        }
        return $options;
    }
}
