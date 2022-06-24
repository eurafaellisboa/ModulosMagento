<?php
namespace Blueskytechco\SetProduct\Setup;

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

        if ($installer->tableExists('blueskytechco_setproduct')) {
            $installer->getConnection()->dropTable($installer->getTable('blueskytechco_setproduct'));
        }
        $table = $installer->getConnection()
            ->newTable($installer->getTable('blueskytechco_setproduct')) 
            ->addColumn('entity_id', Table::TYPE_INTEGER, null, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true
            ], 'Id')
            ->addColumn(
				'name',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Name'
			)
			->addColumn(
				'identifier',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Identifier'
			)
			->addColumn(
				'title',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Title'
			)
			->addColumn(
				'title_link',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Title Link'
			)
			->addColumn(
				'button_style',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Button Style'
			)
			->addColumn(
				'width',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Width'
			)
			->addColumn(
				'height',
				\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				255,
				[ 'nullable' => false],
				'Height'
			)
			->addColumn(
				'banner_image',
				Table::TYPE_TEXT,
				255,
				['nullable' => true, 'default' => null]
			)->addColumn(
				'product_data',
				Table::TYPE_TEXT,
				500,
				['nullable' => true, 'default' => null]
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
