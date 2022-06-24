<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Portfolio\Model\ResourceModel\Portfolio;

use Magento\Store\Model\StoreManagerInterface;
/**
 * Portfolio resource model collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Init resource collection
     *
     * @return void
     */
    protected function _construct()
    {
		$this->_init('MGS\Portfolio\Model\Portfolio', 'MGS\Portfolio\Model\ResourceModel\Portfolio');
		$this->_map['fields']['store'] = 'store_table.store_id';
		$this->_map['fields']['category'] = 'category_table.category_id';
	}
	
	public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => 0], 'public');
		
        return $this;
    }
	
	public function addCategoryFilter($category)
    {

        if (!is_array($category)) {
            $category = [$category];
        }

        $this->addFilter('category', ['in' => $category], 'public');
		
        return $this;
    }
	
	 protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable('stores_portfolio')],
                'main_table.portfolio_id = store_table.portfolio_id',
                []
            );
        }
		
		if ($this->getFilter('category')) {
            $this->getSelect()->join(
                ['category_table' => $this->getTable('mgs_portfolio_category_items')],
                'main_table.portfolio_id = category_table.portfolio_id',
                []
            );
        }
        return parent::_renderFiltersBefore();
    }

}
