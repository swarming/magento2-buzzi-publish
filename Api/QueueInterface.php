<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Api;

interface QueueInterface
{
    /**
     * @param string $eventType
     * @param mixed[] $payload
     * @param int|string $storeId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function add($eventType, array $payload, $storeId);

    /**
     * @param string $eventType
     * @param mixed[] $payload
     * @param int|string $storeId
     * @return bool
     */
    public function send($eventType, array $payload, $storeId);

    /**
     * @param int[] $submissionIds
     * @return int
     */
    public function sendByIds(array $submissionIds);

    /**
     * @param string $eventType
     * @param int|string $storeId
     * @return int
     */
    public function sendByType($eventType, $storeId);

    /**
     * @param int|null $storeId
     * @return int
     */
    public function resendFailed($storeId = null);

    /**
     * @param int $delay
     * @param int|null $storeId
     * @return void
     */
    public function deleteDone($delay, $storeId = null);

    /**
     * @param int[] $submissionIds
     * @return void
     */
    public function deleteByIds(array $submissionIds);
}
