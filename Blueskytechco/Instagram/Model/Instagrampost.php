<?php

namespace Blueskytechco\Instagram\Model;

class Instagrampost extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'blueskytechco_instagram_post';

	protected $_cacheTag = 'blueskytechco_instagram_post';

	protected $_eventPrefix = 'blueskytechco_instagram_post';

	protected function _construct()
	{
		$this->_init('Blueskytechco\Instagram\Model\ResourceModel\Instagrampost');
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