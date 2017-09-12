<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Model\ResourceModel;

use Buzzi\Publish\Api\Data\SubmissionInterface;

class Submission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'buzzi_publish_queue';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, SubmissionInterface::SUBMISSION_ID);
    }

    /**
     * @param \Buzzi\Publish\Model\Submission|\Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getStatus() && $object->getStatus() != SubmissionInterface::STATUS_PENDING) {
            $object->setData(SubmissionInterface::SUBMISSION_TIME, new \Zend_Db_Expr('NOW()'));
        }

        return parent::_beforeSave($object);
    }
}
