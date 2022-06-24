<?php

namespace Blueskytechco\Themeoption\Model\ResourceModel;

class Themeoptionversion extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
   
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) 
	{
        parent::__construct($context, $resourcePrefix);
    }

    protected function _construct()
    {
        $this->_init('blueskytechco_themeoptionversion', 'version_id');
    }
	
	public function getgetLatestVersionResource()
    {
        
		$select = $this->getConnection()->select()->from(
			['cp' => $this->getMainTable()]
		);		
        $select->order('cp.version_id DESC');
        $data = $this->getConnection()->fetchRow($select);
		if(isset($data['version'])){
			return $data['version'];
		}
		return '1';
    }
}
