<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model;

use Buzzi\Publish\Model\ResourceModel\Submission as SubmissionResourceModel;

class Submission extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Buzzi\Publish\Api\Data\SubmissionInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SubmissionResourceModel::class);
    }

    /**
     * @param int $submissionId
     * @return $this
     */
    public function setSubmissionId($submissionId)
    {
        return $this->setData(self::SUBMISSION_ID, $submissionId);
    }

    /**
     * @return int|null
     */
    public function getSubmissionId()
    {
        return $this->_getData(self::SUBMISSION_ID);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_getData(self::STORE_ID);
    }

    /**
     * @param string $eventType
     * @return $this
     */
    public function setEventType($eventType)
    {
        return $this->setData(self::EVENT_TYPE, $eventType);
    }

    /**
     * @return string
     */
    public function getEventType()
    {
        return $this->_getData(self::EVENT_TYPE);
    }

    /**
     * @param bool $useFile
     * @return $this
     */
    public function setUseFile($useFile)
    {
        return $this->setData(self::USE_FILE, $useFile);
    }

    /**
     * @return bool
     */
    public function getUseFile()
    {
        return $this->_getData(self::USE_FILE);
    }

    /**
     * @param string $payload
     * @return $this
     */
    public function setPayload($payload)
    {
        return $this->setData(self::PAYLOAD, $payload);
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->_getData(self::PAYLOAD);
    }

    /**
     * @param int $counter
     * @return $this
     */
    public function setCounter($counter)
    {
        return $this->setData(self::COUNTER, $counter);
    }

    /**
     * @return int
     */
    public function getCounter()
    {
        return $this->_getData(self::COUNTER);
    }

    /**
     * @param string $eventId
     * @return $this
     */
    public function setEventId($eventId)
    {
        return $this->setData(self::EVENT_ID, $eventId);
    }

    /**
     * @return string
     */
    public function getEventId()
    {
        return $this->_getData(self::EVENT_ID);
    }

    /**
     * @param string $creatingTime
     * @return $this
     */
    public function setCreatingTime($creatingTime)
    {
        return $this->setData(self::CREATING_TIME, $creatingTime);
    }

    /**
     * @return string
     */
    public function getCreatingTime()
    {
        return $this->_getData(self::CREATING_TIME);
    }

    /**
     * @param string $submissionTime
     * @return $this
     */
    public function setSubmissionTime($submissionTime)
    {
        return $this->setData(self::SUBMISSION_TIME, $submissionTime);
    }

    /**
     * @return string
     */
    public function getSubmissionTime()
    {
        return $this->_getData(self::SUBMISSION_TIME);
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return int|null
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        return $this->setData(self::ERROR_MESSAGE, $errorMessage);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_getData(self::ERROR_MESSAGE);
    }
}
