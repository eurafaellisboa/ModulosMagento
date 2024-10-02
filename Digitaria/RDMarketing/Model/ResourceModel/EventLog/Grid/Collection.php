<?php
namespace Digitaria\RDMarketing\Model\ResourceModel\EventLog\Grid;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Digitaria\RDMarketing\Model\EventLog as EventLogModel;
use Digitaria\RDMarketing\Model\ResourceModel\EventLog as EventLogResourceModel;



class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'log_id';
	protected $_eventPrefix = 'digitaria_rdmarketing_eventlog_collection';
	protected $_eventObject = 'eventlog_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Digitaria\RDMarketing\Model\EventLog', 'Digitaria\RDMarketing\Model\ResourceModel\EventLog');
	}
	
	public function delete()
    {
        foreach ($this as $eventLog) {
            $eventLog->delete();
        }

        return $this;
    }
}