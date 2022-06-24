<?php
namespace Blueskytechco\CmsPageBanner\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
        $cms_page_table = $installer->getTable('cms_page');

        $connection->addColumn($cms_page_table,'image_field',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255,'nullable' => true,'default' => null, 'comment' => 'Image Field']);
        $connection->addColumn($cms_page_table,'breadcrumbs_position',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255,'nullable' => true,'default' => null, 'comment' => 'breadcrumbs position']);
        $connection->addColumn($cms_page_table,'hide_content_heading',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255,'nullable' => true,'default' => null, 'comment' => 'hide content heading']);
        $connection->addColumn($cms_page_table,'hide_breadcrumbs',['type' =>\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 255,'nullable' => true,'default' => null, 'comment' => 'hide breadcrumbs']);
        $installer->endSetup();
    }
}