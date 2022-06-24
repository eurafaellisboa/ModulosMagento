<?php
namespace Blueskytechco\ProductWidgetAdvanced\Cron\Collect;

class ReviewRate
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
        $maintable = $this->resourceConnection->getTableName('blueskytechco_product_widget_advanced_review_rate');
        $tableData = $this->resourceConnection->getTableName('review_entity_summary');
        $queryTruncate = "TRUNCATE $maintable";
        $this->writeAdapter->query($queryTruncate);
        foreach($this->storeManager->getStores() as $store)
        {
            $storeId = $store->getId();
            $query = "INSERT INTO $maintable(product_id, store_id, rate) SELECT it.entity_pk_value, $storeId, SUM(rating_summary) FROM $tableData AS it WHERE it.store_id = $storeId and it.entity_type = 1 GROUP BY it.entity_pk_value";
            $this->writeAdapter->query($query);
        }
        $queryInsertDefaultStore = "INSERT INTO $maintable(product_id, store_id, rate) SELECT entity_pk_value, 0, SUM(rating_summary) FROM $tableData WHERE store_id != 0 GROUP BY entity_pk_value";
        $this->writeAdapter->query($queryInsertDefaultStore);
        return true;
    }
}
