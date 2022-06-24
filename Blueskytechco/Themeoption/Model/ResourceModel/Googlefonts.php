<?php

namespace Blueskytechco\Themeoption\Model\ResourceModel;

class Googlefonts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('blueskytechco_google_fonts', 'id');
    }
}
