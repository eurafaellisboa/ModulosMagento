<?php
namespace Blueskytechco\SizeChart\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $installer = $setup;
            $installer->startSetup();

            $SizeChartTable = $installer->getConnection()->newTable(
                $installer->getTable('size_chart')
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )->addColumn(
				'store_id',
				Table::TYPE_INTEGER,
				11,
				['nullable' => false]
			)->addColumn(
				'type',
				Table::TYPE_INTEGER,
				11,
				['nullable' => false]
			)->addColumn(
				'content',
				Table::TYPE_TEXT,
				312,
				['nullable' => false]
			)->addColumn(
				'category_ids',
				Table::TYPE_TEXT,
				312,
				['nullable' => false]
			)->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Creation Time'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated Time'
            );
            $installer->getConnection()->createTable($SizeChartTable);
			
            $installer->endSetup();
        }
    }
}
