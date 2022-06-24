<?php

namespace Blueskytechco\Themeoption\Model\ResourceModel\Googlefonts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
	
	protected function _construct()
	{
		$this->_init('Blueskytechco\Themeoption\Model\Googlefonts', 'Blueskytechco\Themeoption\Model\ResourceModel\Googlefonts');
	}
}