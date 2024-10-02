<?php
namespace Digitaria\RDMarketing\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('rdstationmarketing_token'))
            ->addColumn(
                'token_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Token ID'
            )
            ->addColumn(
                'token',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Token'
            )
            ->addColumn(
                'refresh_token',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Refresh Token'
            )
            ->addColumn(
                'expire_time',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Expire Time'
            )
            ->addColumn(
                'generate_time',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Generate Time'
            )
            ->setComment('RD API Token Table');

        $installer->getConnection()->createTable($table);

        $eventLogTable = $installer->getConnection()->newTable(
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
            null,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'status',
            Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Status'
        )->setComment(
            'RD Event Log Table'
        );

        $installer->getConnection()->createTable($eventLogTable);

        $installer->endSetup();
    }
}
