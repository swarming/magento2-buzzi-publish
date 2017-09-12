<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Buzzi\Publish\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Buzzi\Publish\Model\ResourceModel\Submission as SubmissionResourceModel;
use Buzzi\Publish\Api\Data\SubmissionInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createPublishQueueTable($setup);

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    protected function createPublishQueueTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(SubmissionResourceModel::TABLE_NAME)
        )->addColumn(
            SubmissionInterface::SUBMISSION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'primary' => true, 'unsigned' => true, 'nullable' => false],
            'Id'
        )->addColumn(
            SubmissionInterface::STORE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addColumn(
            SubmissionInterface::EVENT_TYPE,
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Event Type'
        )->addColumn(
            SubmissionInterface::USE_FILE,
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Whether payload is filename or data'
        )->addColumn(
            SubmissionInterface::PAYLOAD,
            Table::TYPE_TEXT,
            SubmissionInterface::MAX_PAYLOAD_LENGTH,
            ['nullable' => false, 'default' => ''],
            'Payload'
        )->addColumn(
            SubmissionInterface::COUNTER,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Counter'
        )->addColumn(
            SubmissionInterface::EVENT_ID,
            Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Event ID'
        )->addColumn(
            SubmissionInterface::CREATING_TIME,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Creating Time'
        )->addColumn(
            SubmissionInterface::SUBMISSION_TIME,
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Submission Time'
        )->addColumn(
            SubmissionInterface::STATUS,
            Table::TYPE_TEXT,
            15,
            ['nullable' => false, 'default' => SubmissionInterface::STATUS_PENDING],
            'Status'
        )->addColumn(
            SubmissionInterface::ERROR_MESSAGE,
            Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Error Message'
        )->addIndex(
            $setup->getIdxName(SubmissionResourceModel::TABLE_NAME, [SubmissionInterface::STORE_ID], AdapterInterface::INDEX_TYPE_INDEX),
            [SubmissionInterface::STORE_ID],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(SubmissionResourceModel::TABLE_NAME, [SubmissionInterface::EVENT_TYPE], AdapterInterface::INDEX_TYPE_INDEX),
            [SubmissionInterface::EVENT_TYPE],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(SubmissionResourceModel::TABLE_NAME, [SubmissionInterface::STATUS], AdapterInterface::INDEX_TYPE_INDEX),
            [SubmissionInterface::STATUS],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(SubmissionResourceModel::TABLE_NAME, [SubmissionInterface::SUBMISSION_TIME], AdapterInterface::INDEX_TYPE_INDEX),
            [SubmissionInterface::SUBMISSION_TIME],
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment(
            'Buzzi Publish Queue'
        );
        $setup->getConnection()->createTable($table);
    }
}
