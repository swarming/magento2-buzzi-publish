<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config\Structure;

class GroupGenerator
{
    /**
     * @var \Buzzi\Publish\Model\Config\General
     */
    protected $configGeneral;

    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    protected $configEvents;

    /**
     * @var \Buzzi\Base\Helper\Config\Structure\CronFieldsGenerator
     */
    protected $cronFieldsGenerator;

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\FlyweightFactory
     */
    protected $flyweightFactory;

    /**
     * @var \Magento\Config\Model\Config\ScopeDefiner
     */
    protected $scopeDefiner;

    /**
     * @param \Buzzi\Publish\Model\Config\General $configGeneral
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Buzzi\Base\Helper\Config\Structure\CronFieldsGenerator $cronFieldsGenerator
     * @param \Magento\Config\Model\Config\Structure\Element\FlyweightFactory $flyweightFactory
     * @param \Magento\Config\Model\Config\ScopeDefiner $scopeDefiner
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\General $configGeneral,
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Buzzi\Base\Helper\Config\Structure\CronFieldsGenerator $cronFieldsGenerator,
        \Magento\Config\Model\Config\Structure\Element\FlyweightFactory $flyweightFactory,
        \Magento\Config\Model\Config\ScopeDefiner $scopeDefiner
    ) {
        $this->configGeneral = $configGeneral;
        $this->configEvents = $configEvents;
        $this->cronFieldsGenerator = $cronFieldsGenerator;
        $this->flyweightFactory = $flyweightFactory;
        $this->scopeDefiner = $scopeDefiner;
    }

    /**
     * @param array $predefinedGroups
     * @return array
     */
    public function generate(array $predefinedGroups = [])
    {
        $allTypes = $this->configEvents->getAllTypes();
        $enabledEvents = $this->configGeneral->getEnabledEvents();

        $sectionGroups = [];
        foreach ($allTypes as $eventType) {
            if (!in_array($eventType, $enabledEvents)) {
                continue;
            }

            $groupData = $this->generateEventConfig($eventType, $this->getPredefinedEventFields($eventType, $predefinedGroups));
            $sectionGroups[$this->configEvents->getEventCode($eventType)] = $this->createGroup($groupData);
        }

        return $sectionGroups;
    }

    /**
     * @param string $eventType
     * @param array $predefinedGroups
     * @return array
     */
    protected function getPredefinedEventFields($eventType, $predefinedGroups)
    {
        $eventCode = $this->configEvents->getEventCode($eventType);
        return !empty($predefinedGroups[$eventCode]['children']) ? $predefinedGroups[$eventCode]['children'] : [];
    }

    /**
     * @param string $eventType
     * @param array $predefinedFields
     * @return array
     */
    protected function generateEventConfig($eventType, array $predefinedFields = [])
    {
        $groupId = $this->configEvents->getEventCode($eventType);
        $groupData = [
            '_elementType' => 'group',
            'id' => $groupId,
            'label' => $this->configEvents->getEventLabel($eventType),
            'path' => 'buzzi_publish_events',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '0',
            'type' => 'text',
            'children' => array_merge_recursive(
                $predefinedFields,
                $this->cronFieldsGenerator->generate('buzzi_publish_events/' . $groupId, $this->configEvents->isCronOnly($eventType))
            )
        ];
        return $groupData;
    }

    /**
     * @param array $groupData
     * @return \Magento\Config\Model\Config\Structure\Element\Group
     */
    protected function createGroup(array $groupData)
    {
        /** @var \Magento\Config\Model\Config\Structure\Element\Group $group */
        $group = $this->flyweightFactory->create('group');
        $group->setData($groupData, $this->scopeDefiner->getScope());
        return $group;
    }
}
