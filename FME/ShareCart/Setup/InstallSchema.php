<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Setup;

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
        // Get v table
        $tableName = $installer->getTable('fme_sharecart');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'sharecart_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'customer_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                        'nullable' => true
                        ],
                        'Customer ID'
                    )
                    ->addColumn(
                        'quote_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                        'nullable' => true
                        ],
                        'Quote ID'
                    )->addColumn(
                        'sharing_method',
                        Table::TYPE_SMALLINT,
                        null,
                        [
                        'nullable' => false,
                        'default' => 0,
                        ],
                        'Sharing Method'
                    ) ->addColumn(
                        'sharing_date',
                        Table::TYPE_TIMESTAMP,
                        null,
                        [
                        'nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Sharing Date'
                    )->addColumn(
                        'share_from',
                        Table::TYPE_TEXT,
                        null,
                        [
                        'nullable' => true
                        ],
                        'Share From'
                    )->addColumn(
                        'share_to',
                        Table::TYPE_TEXT,
                        null,
                        [
                        'nullable' => true
                        ],
                        'Share To'
                    )->addColumn(
                        'message',
                        Table::TYPE_TEXT,
                        null,
                        [
                        'nullable' => true
                        ],
                        'Message'
                    )->addColumn(
                        'grand_total',
                        Table::TYPE_INTEGER,
                        null,
                        [
                        'nullable' => false,
                        'default' => 0,
                        ],
                        'priority'
                    )->setComment('main Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

       
       

        $installer->endSetup();
    }
}
