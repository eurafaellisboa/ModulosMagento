<?php
namespace Blueskytechco\SizeChart\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\DataObjectFactory;

class SizeChart extends AbstractDb
{
    private $date;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        DateTime $date,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
        $this->date = $date;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('size_chart', 'entity_id');
    }
	
    public function getAttributeProduct()
    {
        $table = $this->getTable('eav_attribute');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('entity_type_id = ?', 1);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }

}
