<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Service;

use Buzzi\Publish\Api\PackerInterface;

class PackerRepository
{
    /**
     * @var \Buzzi\Publish\Model\Config\Events
     */
    private $configEvents;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Buzzi\Publish\Model\Config\Events $configEvents
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Buzzi\Publish\Model\Config\Events $configEvents,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->configEvents = $configEvents;
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $eventType
     * @return \Buzzi\Publish\Api\PackerInterface
     */
    public function getPacker($eventType)
    {
        $packerModel = $this->configEvents->getPackerModel($eventType);
        if (!$packerModel) {
            throw new \DomainException(sprintf('Packer for %s event type is not defined.', $eventType));
        }

        $packer = $this->objectManager->get($packerModel);
        if (!$packer instanceof PackerInterface) {
            throw new \DomainException(
                sprintf('Wrong packer for %s event type, it must implement % interface.', $eventType, PackerInterface::class)
            );
        }
        return $packer;
    }
}
