<?php
namespace Blueskytechco\ProductWidgetAdvanced\Model\ResourceModel\MostViewed;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'id';
	protected $_eventPrefix = 'blueskytechco_product_index_most_viewed_collection';
	protected $_eventObject = 'product_index_most_viewed_collection';

	protected function _construct()
	{
		$this->_init('Blueskytechco\ProductWidgetAdvanced\Model\MostViewed', 'Blueskytechco\ProductWidgetAdvanced\Model\ResourceModel\MostViewed');
	}

}