<?php
namespace Blueskytechco\SetProduct\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Magento\Store\Model\StoreManagerInterface;

class ProductSet extends AbstractResource
{
    
    protected $_date;
    protected $_storeManager;

    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        $this->_date         = $date;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('blueskytechco_setproduct', 'entity_id'); 
    }

    public function _beforeSave(AbstractModel $object)
    {

        return $this;
    }
}
