<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\Config\Structure\Event;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $output = [];

        /** @var \DOMElement[] $buzziNodes */
        $buzziNodes = $source->getElementsByTagName('buzzi');

        foreach ($buzziNodes as $buzziNode) {
            /** @var \DOMElement $publishEvent */
            foreach ($buzziNode->childNodes as $publishEvent) {
                if ($publishEvent->nodeName != 'publish_event') {
                    continue;
                }

                $eventData = $this->convertEvent($publishEvent);

                if (empty($eventData['type'])) {
                    throw new \InvalidArgumentException('Event "type" does not exist');
                }

                $output[$eventData['type']] = $eventData;
            }
        }

        return $output;
    }

    /**
     * @param \DOMElement $publishEvent
     * @return mixed[]
     */
    protected function convertEvent($publishEvent)
    {
        if (!$publishEvent->hasAttribute('code')) {
            throw new \InvalidArgumentException('Event attribute "code" does not exist');
        }

        $eventData = [];
        $eventData['code'] = $publishEvent->getAttribute('code');
        $eventData['cron_only'] = $publishEvent->hasAttribute('cron_only') && $this->isValueTrue($publishEvent->getAttribute('cron_only'));

        foreach ($publishEvent->childNodes as $childNode) {
            if ($childNode->nodeName == 'label') {
                $eventData['label'] = $childNode->nodeValue;
            }
            if ($childNode->nodeName == 'type') {
                $eventData['type'] = $childNode->nodeValue;
            }
            if ($childNode->nodeName == 'cron_model') {
                $eventData['cron_model'] = $childNode->nodeValue;
            }
            if ($childNode->nodeName == 'packer_model') {
                $eventData['packer_model'] = $childNode->nodeValue;
            }
        }

        return $eventData;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function isValueTrue($value)
    {
        return $value === 'true' || $value == '1';
    }
}
