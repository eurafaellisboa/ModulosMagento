<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Portfolio\Model\ResourceModel;
use MGS\Portfolio\Model\Category as ModelCategory;
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mgs_portfolio_category', 'category_id');
    }
	
	/**
     * Process block data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
	 protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
			$categoryId = $object->getId();
            $storeIds = $this->lookupStoreIds($categoryId);
            $object->setData('store_id', $storeIds);
            $object->setData('stores', $storeIds);
        }

        return parent::_afterLoad($object);
    }
	 
	/**
     * Process block data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['category_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('mgs_portfolio_category_items'), $condition);
        $this->getConnection()->delete($this->getTable('stores_portfolio_category'), $condition);

        return parent::_beforeDelete($object);
    }
	 
    
	protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
		$connection = $this->getConnection();
        
        $connection->delete($this->getTable('stores_portfolio_category'), ['category_id = ?' => $object->getId()]);
		
		$portfolio = $object->getStoreIds();
		if (!is_array($portfolio)) {
            $portfolio = [];
        }
		
		
		foreach ($portfolio as $stores) {
            $data = [];
            $data['store_id'] = $stores;
            $data['category_id'] = $object->getId();
            $connection->insert($this->getTable('stores_portfolio_category'), $data);
        }
		return $this;
		
		return parent::_afterSave($object);
    }
	
	public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('stores_portfolio_category'),
            'store_id'
        )->where(
            'category_id = :category_id'
        );

        $binds = [':category_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }
	
	
	
	 public function getStores(ModelCategory $category)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('stores_portfolio_category'),
            'store_id'
        )->where(
            'category_id = :category_id'
        );

        if (!($result = $connection->fetchCol($select, ['category_id' => $category->getId()]))) {
            $result = [];
        }

        return $result;
    }
	public function joinFilter($collection, $categoryId){
		$tableName = $this->getTable('stores_portfolio_category');
		$collection->getSelect()->joinLeft(array('cat_items'=>$tableName), 'main_table.store_id =cat_items.store_id', array(''))->where('(cat_items.store_id = '.$categoryId.')');
		return $collection;
	}
}
