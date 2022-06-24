<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Portfolio\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();
        
        
        
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('stores_portfolio'))
                
                ->addColumn(
                        'portfolio_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Portfolio Id'
                )
                ->addColumn(
                        'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Store Id'
                )
                ->addIndex(
                        $setup->getIdxName('stores_portfolio', ['store_id']), ['store_id']
                )
                ->addForeignKey(
                        $setup->getFkName('stores_portfolio', 'portfolio_id', 'mgs_portfolio_items', 'portfolio_id'), 'portfolio_id', $setup->getTable('mgs_portfolio_items'), 'portfolio_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                        $setup->getFkName('stores_portfolio', 'store_id', 'store', 'store_id'), 'store_id', $setup->getTable('store'), 'store_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Portfolio of Store');
				 $setup->getConnection()->createTable($table);
         
        }
		if (version_compare($context->getVersion(), '1.0.2') <= 0) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('stores_portfolio_category'))
                
                ->addColumn(
                        'category_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Category Id'
                )
                ->addColumn(
                        'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Store Id'
                )
                ->addIndex(
                        $setup->getIdxName('stores_portfolio_category', ['store_id']), ['store_id']
                )
                ->addForeignKey(
                        $setup->getFkName('stores_portfolio_category', 'category_id', 'mgs_portfolio_category', 'category_id'), 'category_id', $setup->getTable('mgs_portfolio_category'), 'category_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                        $setup->getFkName('stores_portfolio_category', 'store_id', 'store', 'store_id'), 'store_id', $setup->getTable('store'), 'store_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Portfolio Category of Store');
				 $setup->getConnection()->createTable($table);
         
        }
        $setup->endSetup();
    }

}
