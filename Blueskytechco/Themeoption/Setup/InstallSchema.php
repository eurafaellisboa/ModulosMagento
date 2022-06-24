<?php

namespace Blueskytechco\Themeoption\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('blueskytechco_themeoptionversion')
        )->addColumn(
            'version_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Version ID'
        )->addColumn(
            'version',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Version'
        )->addColumn(
            'version_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Version Update Time'
        );
        $installer->getConnection()->createTable($table);

        $installer->getConnection()->dropTable($installer->getTable('blueskytechco_google_fonts'));
        $table_google_fonts = $installer->getConnection()
        ->newTable($installer->getTable('blueskytechco_google_fonts'))
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )
        ->addColumn(
            'family',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Family'
        )->setComment('Google Fonts');

        $installer->getConnection()->createTable($table_google_fonts);

        $installer->endSetup();
    }
}
