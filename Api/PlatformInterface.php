<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Api;

interface PlatformInterface
{
    /**
     * @param string $eventType
     * @param mixed[] $payload
     * @param int $storeId
     * @return string
     */
    public function send($eventType, $payload, $storeId);

    /**
     * @param mixed[] $multipart
     * @param int|null $storeId
     * @return void
     */
    public function upload($multipart, $storeId);
}
