<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Api;

interface PackerManagerInterface
{
    /**
     * @param string $eventType
     * @param mixed $inputData
     * @param int|null $customerId
     * @param string|null $guestEmail
     * @return void
     */
    public function deliver($eventType, $inputData, $customerId = null, $guestEmail = null);
}
