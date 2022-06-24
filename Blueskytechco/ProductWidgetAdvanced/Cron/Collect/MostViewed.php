<?php
namespace Blueskytechco\ProductWidgetAdvanced\Cron\Collect;

class MostViewed
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
        $maintable = $this->resourceConnection->getTableName('blueskytechco_product_widget_advanced_most_viewed');
        $tableData = $this->resourceConnection->getTableName('blueskytechco_product_index_most_viewed');
        $queryTruncate = "TRUNCATE $maintable";
        $this->writeAdapter->query($queryTruncate);
        foreach($this->storeManager->getStores() as $store)
        {
            $storeId = $store->getId();
            $query = "INSERT INTO $maintable(product_id, store_id, viewed) SELECT it.product_id, $storeId, COUNT(*) FROM $tableData AS it WHERE it.store_id = $storeId GROUP BY it.product_id";
            $this->writeAdapter->query($query);
        }
        $queryInsertDefaultStore = "INSERT INTO $maintable(product_id, store_id, viewed) SELECT product_id, 0, COUNT(product_id) FROM $tableData WHERE store_id != 0 GROUP BY product_id";
        $this->writeAdapter->query($queryInsertDefaultStore);

        return true;
    }
}
