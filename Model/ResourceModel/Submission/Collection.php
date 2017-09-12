<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\ResourceModel\Submission;

use Buzzi\Publish\Model\ResourceModel\Submission as SubmissionResourceModel;
use Buzzi\Publish\Api\Data\SubmissionInterface;
use Buzzi\Publish\Model\Submission;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Submission::class, SubmissionResourceModel::class);
    }

    /**
     * @param int[] $submissionIds
     * @return $this
     */
    public function filterSubmissionIds(array $submissionIds)
    {
        $this->addFilter(SubmissionInterface::SUBMISSION_ID, ['in' => $submissionIds], 'public');
        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function filterStore($storeId)
    {
        $this->addFilter(SubmissionInterface::STORE_ID, $storeId);
        return $this;
    }

    /**
     * @param string $eventType
     * @return $this
     */
    public function filterEventType($eventType)
    {
        $this->addFilter(SubmissionInterface::EVENT_TYPE, $eventType);
        return $this;
    }

    /**
     * @return $this
     */
    public function filterDone()
    {
        $this->addFieldToFilter(SubmissionInterface::STATUS, ['eq' => SubmissionInterface::STATUS_DONE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function filterNotDone()
    {
        $this->addFieldToFilter(SubmissionInterface::STATUS, ['neq' => SubmissionInterface::STATUS_DONE]);
        return $this;
    }

    /**
     * @return $this
     */
    public function filterPending()
    {
        $this->addFilter(SubmissionInterface::STATUS, SubmissionInterface::STATUS_PENDING);
        return $this;
    }

    /**
     * @param int $maxTimes
     * @return $this
     */
    public function filterFailed($maxTimes = 0)
    {
        $this->addFilter(SubmissionInterface::STATUS, SubmissionInterface::STATUS_FAIL);
        if ($maxTimes > 0) {
            $this->addFilter(SubmissionInterface::COUNTER, ['lteq' => $maxTimes], 'public');
        }
        return $this;
    }

    /**
     * @param int $days
     * @return $this
     */
    public function filterSubmissionTime($days)
    {
        $this->addFilter(
            SubmissionInterface::SUBMISSION_TIME,
            ['lteq' => new \Zend_Db_Expr(sprintf('DATE_SUB(NOW(), INTERVAL %d DAY)', $days))],
            'public'
        );
        return $this;
    }
}
