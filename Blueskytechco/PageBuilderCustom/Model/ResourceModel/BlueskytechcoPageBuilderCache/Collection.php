<?php
namespace Blueskytechco\PageBuilderCustom\Model\ResourceModel\BlueskytechcoPageBuilderCache;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'entity_id';
	protected $_eventPrefix = 'blueskytechco_page_builder_cache_collection';
	protected $_eventObject = 'blueskytechco_page_builder_cache_collection_obj';

	protected function _construct()
	{
		$this->_init('Blueskytechco\PageBuilderCustom\Model\BlueskytechcoPageBuilderCache', 'Blueskytechco\PageBuilderCustom\Model\ResourceModel\BlueskytechcoPageBuilderCache');
	}

}