<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Service;

class Platform implements \Buzzi\Publish\Api\PlatformInterface
{
    /**
     * @var \Buzzi\Base\Platform\Registry
     */
    protected $platformRegistry;

    /**
     * @param \Buzzi\Base\Platform\Registry $platformRegistry
     */
    public function __construct(
        \Buzzi\Base\Platform\Registry $platformRegistry
    ) {
        $this->platformRegistry = $platformRegistry;
    }

    /**
     * @param int|null $storeId
     * @return \Buzzi\Publish\PublishService
     */
    protected function getPublishService($storeId)
    {
        return $this->platformRegistry->getSdk($storeId)->getPublishService();
    }

    /**
     * @param string $eventType
     * @param mixed[] $payload
     * @param int $storeId
     * @return string
     */
    public function send($eventType, $payload, $storeId)
    {
        return $this->getPublishService($storeId)->send($eventType, (array)$payload);
    }

    /**
     * @param array $multipart
     * @param int|null $storeId
     * @return void
     */
    public function upload($multipart, $storeId)
    {
        $this->getPublishService($storeId)->upload($multipart);
    }
}
