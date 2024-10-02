<?php
namespace Digitaria\RDMarketing\Model;
class EventLog extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'digitaria_rdmarketing_eventlog';

	protected $_cacheTag = 'digitaria_rdmarketing_eventlog';

	protected $_eventPrefix = 'digitaria_rdmarketing_eventlog';

	protected function _construct()
	{
		$this->_init('Digitaria\RDMarketing\Model\ResourceModel\EventLog');
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
