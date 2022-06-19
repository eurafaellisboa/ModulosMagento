<?php

namespace Abzertech\Smtp\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Install
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return null
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('abzertech_smtp_log')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Log Id'
        )->addColumn(
            'recipient',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Recipient'
        )->addColumn(
            'sender',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Sender'
        )->addColumn(
            'subject',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => true, 'nullable' => false],
            'Subject'
        )->addColumn(
            'body',
            Table::TYPE_TEXT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Content'
        )->addColumn(
            'sent_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
            'Sent At'
        )->setComment('Abzer SMTP Log Table')
                ->setOption('charset', 'utf8');

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
