<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Setup;

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

            $menuBuilderTable = $installer->getConnection()->newTable(
                $installer->getTable('menu_builder')
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
                'identifier',
                Table::TYPE_TEXT,
                255,
                [],
                'Identifier'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                [],
                'Name'
            )->addColumn(
                'type',
                Table::TYPE_INTEGER,
                11,
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
            $installer->getConnection()->createTable($menuBuilderTable);
            
            $menuBuilderItemTable = $installer->getConnection()->newTable(
                $installer->getTable('menu_builder_item')
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
                'menu_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false]
            )->addColumn(
                'menu_order',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false]
            )->addColumn(
                'item_title',
                Table::TYPE_TEXT,
                312,
                ['nullable' => false]
            )->addColumn(
                'type_menu',
                Table::TYPE_TEXT,
                312,
                ['nullable' => false]
            )->addColumn(
                'menu_object_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false]
            )->addColumn(
                'status',
                Table::TYPE_INTEGER,
                11,
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
            $installer->getConnection()->createTable($menuBuilderItemTable);
            
            $menuBuilderItemMetaTable = $installer->getConnection()->newTable(
                $installer->getTable('menu_builder_item_meta')
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
                'menu_item_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false]
            )->addColumn(
                'meta_key',
                Table::TYPE_TEXT,
                312,
                ['nullable' => false]
            )->addColumn(
                'meta_value',
                Table::TYPE_TEXT,
                312,
                ['nullable' => true]
            );
            $installer->getConnection()->createTable($menuBuilderItemMetaTable);

            $menuBuilderFlatDataTable = $installer->getConnection()->newTable(
                $installer->getTable('menu_builder_flat_data')
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
                'menu_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false]
            )->addColumn(
                'identifier',
                Table::TYPE_TEXT,
                255,
                [],
                'Identifier'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false]
            )->addColumn(
                'content_html',
                Table::TYPE_TEXT,
                312,
                [],
                'Content Html'
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
            $installer->getConnection()->createTable($menuBuilderFlatDataTable);
            
            $installer->endSetup();
        }
    }
}
