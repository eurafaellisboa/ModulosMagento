<?php
namespace Blueskytechco\ProductWidgetAdvanced\Cron\Collect;

class BestSeller
{
    protected $resourceConnection;
    protected $readAdapter;
    protected $writeAdapter;
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->writeAdapter = $this->resourceConnection->getConnection('core_write');
    }
    public function execute()
    {
        $tableBestSeller = $this->resourceConnection->getTableName('blueskytechco_product_widget_advanced_bestseller');
        $tableSaleItem = $this->resourceConnection->getTableName('sales_order_item');
        $queryTruncate = "TRUNCATE $tableBestSeller";
        $this->writeAdapter->query($queryTruncate);

        foreach($this->storeManager->getStores() as $store)
        {
            $storeId = $store->getId();
            $query = "INSERT INTO $tableBestSeller(product_id, store_id, bestseller) SELECT it.product_id, $storeId, SUM(it.qty_ordered) FROM $tableSaleItem AS it WHERE it.store_id = $storeId GROUP BY it.product_id";
            $this->writeAdapter->query($query);
        }

        $queryInsertDefaultStore = "INSERT INTO $tableBestSeller(product_id, store_id, bestseller) SELECT product_id, 0, SUM(bestseller) FROM $tableBestSeller WHERE store_id != 0 GROUP BY product_id";
        $this->writeAdapter->query($queryInsertDefaultStore);

        return true;
    }
}
