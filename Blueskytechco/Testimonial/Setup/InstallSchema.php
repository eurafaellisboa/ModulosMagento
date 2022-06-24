<?php

namespace Blueskytechco\Testimonial\Setup;

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

        $table = $installer->getConnection()
            ->newTable($installer->getTable('blueskytechco_testimonial'))
            ->addColumn(
                'testimonial_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Testimonial ID'
            )
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Name')
            ->addColumn('image', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
            ->addColumn('text', Table::TYPE_TEXT, '1M', [], 'Text')
            ->addColumn('job', Table::TYPE_TEXT, 255, ['nullable' => true, 'default' => null])
            ->addColumn('rating_summary', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '5'], 'Rating Summary')
            ->addColumn('order', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Order')
            ->addColumn('created_time', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Created Time')
            ->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => true, 'default' => null], 'Update Time')
            ->addIndex($installer->getIdxName('testimonial_id', ['testimonial_id']), ['testimonial_id'])
            ->setComment('Blueskytechco Testimonial');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}
