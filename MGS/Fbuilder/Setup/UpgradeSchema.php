<?php
/**
* Copyright © 2016 MGS-THEMES. All rights reserved.
*/

namespace MGS\Fbuilder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.0', '<=')) {
			$connection = $setup->getConnection();
			
			$sectionTable = $setup->getTable('mgs_fbuilder_section');
			
            if ($connection->isTableExists($sectionTable) == true) {
				$connection->addColumn($sectionTable, 'no_padding', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'fullwidth',
					'comment' => 'Is No Padding'
				]);
			}
		}
		
		if (version_compare($context->getVersion(), '1.0.1', '<=')) {
			$connection = $setup->getConnection();
			
			$sectionTable = $setup->getTable('mgs_fbuilder_section');
			
            if ($connection->isTableExists($sectionTable) == true) {
				$connection->addColumn($sectionTable, 'hide_desktop', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'no_padding',
					'comment' => 'Hide on Desktop'
				]);
				
				$connection->addColumn($sectionTable, 'hide_tablet', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'hide_desktop',
					'comment' => 'Hide on Tablet'
				]);
				
				$connection->addColumn($sectionTable, 'hide_mobile', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'hide_tablet',
					'comment' => 'Hide on Mobile'
				]);
			}
			
			$childTable = $setup->getTable('mgs_fbuilder_child');
			
            if ($connection->isTableExists($childTable) == true) {
				$connection->addColumn($childTable, 'hide_desktop', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'background_cover',
					'comment' => 'Hide on Desktop'
				]);
				
				$connection->addColumn($childTable, 'hide_tablet', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'hide_desktop',
					'comment' => 'Hide on Tablet'
				]);
				
				$connection->addColumn($childTable, 'hide_mobile', [
					'type' => Table::TYPE_SMALLINT,
					'nullable' => true,
					'after' => 'hide_tablet',
					'comment' => 'Hide on Mobile'
				]);
			}
		}
		
        $setup->endSetup();
    }
}