<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Api\Data;

use Magento\Framework\DB\Ddl\Table as DBTable;

interface SubmissionInterface
{
    const STATUS_PENDING = 'pending';
    const STATUS_FAIL = 'fail';
    const STATUS_DONE = 'done';

    const SUBMISSION_ID = 'submission_id';
    const STORE_ID = 'store_id';
    const EVENT_TYPE = 'event_type';
    const USE_FILE = 'use_file';
    const PAYLOAD = 'payload';
    const COUNTER = 'counter';
    const EVENT_ID = 'event_id';
    const CREATING_TIME = 'creating_time';
    const SUBMISSION_TIME = 'submission_time';
    const STATUS = 'status';
    const ERROR_MESSAGE = 'error_message';

    const MAX_PAYLOAD_LENGTH = DBTable::MAX_TEXT_SIZE;

    /**
     * @param int $submissionId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setSubmissionId($submissionId);

    /**
     * @return int|null
     */
    public function getSubmissionId();

    /**
     * @param int $storeId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param string $eventType
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setEventType($eventType);

    /**
     * @return string
     */
    public function getEventType();

    /**
     * @param bool $useFile
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setUseFile($useFile);

    /**
     * @return bool
     */
    public function getUseFile();

    /**
     * @param string $payload
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setPayload($payload);

    /**
     * @return string
     */
    public function getPayload();

    /**
     * @param int $counter
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setCounter($counter);

    /**
     * @return int
     */
    public function getCounter();

    /**
     * @param string $eventId
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setEventId($eventId);

    /**
     * @return string
     */
    public function getEventId();

    /**
     * @param string $creatingTime
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setCreatingTime($creatingTime);

    /**
     * @return string
     */
    public function getCreatingTime();

    /**
     * @param string $submissionTime
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setSubmissionTime($submissionTime);

    /**
     * @return string
     */
    public function getSubmissionTime();

    /**
     * @param int $status
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setStatus($status);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param string $errorMessage
     * @return \Buzzi\Publish\Api\Data\SubmissionInterface
     */
    public function setErrorMessage($errorMessage);

    /**
     * @return string
     */
    public function getErrorMessage();
}
