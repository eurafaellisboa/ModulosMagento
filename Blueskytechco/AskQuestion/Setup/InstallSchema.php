<?php
/**
 * Copyright © 2022 Blueskytechco. All rights reserved.
 */
namespace Blueskytechco\AskQuestion\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $storeTable = $installer->getTable('askquestion_questions');

        $table = $installer->getConnection()->newTable(
            $storeTable
        )->addColumn(
            'question_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Question ID'
        )->addColumn(
            'customer_name',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            312,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null]
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false, 'default' => null]
        )->addColumn(
            'product_id',
            Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null]
        )->addColumn(
        'created_at',
        Table::TYPE_TIMESTAMP,
        null,
        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
        'Created At');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
