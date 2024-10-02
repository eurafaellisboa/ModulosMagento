<?php
namespace Digitaria\RDMarketing\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->createEventLogTable($installer);
        }

        $installer->endSetup();
    }

    private function createEventLogTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('rdstationmarketing_log')
        )->addColumn(
            'log_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Log ID'
        )->addColumn(
            'event_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Event Type'
		   )->addColumn(
            'conversion',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Conversion'	  	  
        )->addColumn(
            'event_date',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Event Date'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Status'
        )->setComment(
            'RD Event Log Table'
        );

        $installer->getConnection()->createTable($table);
    }
}
