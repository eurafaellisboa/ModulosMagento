<?php

namespace Blueskytechco\ProductWidgetAdvanced\Model;

class MostViewed extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'blueskytechco_product_index_most_viewed';

	protected $_cacheTag = 'blueskytechco_product_index_most_viewed';

	protected $_eventPrefix = 'blueskytechco_product_index_most_viewed';

	protected function _construct()
	{
		$this->_init('Blueskytechco\ProductWidgetAdvanced\Model\ResourceModel\MostViewed');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}