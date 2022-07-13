<?php


namespace MestreMage\Cielo\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //Your install script
        $setup->startSetup();
        $context->getVersion();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('mestremage_card')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true],
            'ID'
        )->addColumn(
            'id_customer',
            Table::TYPE_TEXT,
            255,
            [],
            'id_customer'
        )->addColumn(
            'hash',
            Table::TYPE_TEXT,
            999,
            [],
            'hash'
        )->addColumn(
            'part_number',
            Table::TYPE_TEXT,
            255,
            [],
            'part_number'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
        //Your install script
    }
}