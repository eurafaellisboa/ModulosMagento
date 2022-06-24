<?php
namespace Blueskytechco\OnePageCheckout\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;


class InstallData implements InstallDataInterface
{
    /**
     * @var Magento\Sales\Setup\SalesSetupFactory
     */
    protected $_salesSetupFactory;

    /**
     * @var Magento\Quote\Setup\QuoteSetupFactory
     */
    protected $_quoteSetupFactory;

    /**
     * @param SalesSetupFactory $salesSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->_salesSetupFactory = $salesSetupFactory;
        $this->_quoteSetupFactory = $quoteSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        // @var \Magento\Sales\Setup\SalesSetup $salesInstaller
        $columnExist = $installer->getConnection()->tableColumnExists(
            $installer->getTable('sales_order'),
            'customer_shipping_date'
        );
        if (!$columnExist) {
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'customer_shipping_date',
                [
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Customer Shipping Delivery Date'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'customer_shipping_comments',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Customer Shipping Delivery Comment'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'customer_shipping_date',
                [
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Customer Shipping Delivery Date'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'customer_shipping_comments',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Customer Shipping Delivery Comment'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_grid'),
                'customer_shipping_date',
                [
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Customer Shipping Delivery Date'
                ]
            );
        }

        $setup->endSetup();
    }
}
