<?php

namespace VimirLab\CategoryFee\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
	const MODULE_NAME = 'VimirLab_CategoryFee';
	
	const XML_PATH_ENABLE  = 'categoryfee/general/enable';

	protected $_store;
	
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Module\Manager $moduleManager
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
		$this->moduleManager = $moduleManager;
    }
	
	public function getScopeConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
	
	public function isEnabled($storeId = null)
    {
        if ($this->moduleManager->isOutputEnabled(self::MODULE_NAME) && $this->getScopeConfig(self::XML_PATH_ENABLE, $storeId)) {
			return true;
		}
		return false;
    }
	
	public function getTotalSummaryLabel($storeId = null)
    {
        return __('Taxa de visita');
    }
	
	public function getStore() 
	{
		if(is_null($this->_store)) {
			$this->_store =  $this->storeManager->getStore();
		}
		return $this->_store;
	}
}