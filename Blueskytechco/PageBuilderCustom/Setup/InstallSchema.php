<?php
namespace Blueskytechco\PageBuilderCustom\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if ($installer->tableExists('blueskytechco_page_builder_cache')) {
            $installer->getConnection()->dropTable($installer->getTable('blueskytechco_page_builder_cache'));
        }
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blueskytechco_page_builder_cache')) 
            ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true
            ], 'Id')
            ->addColumn(
				'cache_identifier',
				Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Cache Identifier'
			)->addColumn(
				'serialize_data',
				Table::TYPE_TEXT,
				null,
				['nullable' => false],
				'Serialize Data'
			)->addColumn(
				'created_at',
				Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
				'Created At'
			)->addColumn(
				'updated_at',
				Table::TYPE_TIMESTAMP,
				null,
				['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
				'Updated At'
			);

        $installer->getConnection()->createTable($table); 

        $installer->endSetup();
    }
}
