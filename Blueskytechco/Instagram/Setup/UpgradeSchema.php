<?php
namespace Blueskytechco\Instagram\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.4.2', '<')) {
            $installer->getConnection()->dropTable($installer->getTable('blueskytechco_instagram'));
            $table = $installer->getConnection()
                ->newTable($installer->getTable('blueskytechco_instagram'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'media_url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'media_url'
                )
                ->addColumn(
                    'local_media_url',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'local_media_url'
                )
                ->addColumn(
                    'id_instagram',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'id_instagram'
                )
                ->addColumn(
                    'username',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'username'
                )
                ->addColumn(
                    'permalink',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'permalink'
                )
                ->addColumn(
                    'caption',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true],
                    'Caption'
                )
                ->addColumn(
                    'store',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    ['nullable' => true],
                    'Store'
                )
                ->setComment('Instagram');

            $installer->getConnection()->createTable($table);
        }
		
        $setup->endSetup();
    }
}
