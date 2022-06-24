<?php
/**
 * Copyright © 2016 tonypham.web.developer@gmail.com
 */

namespace Blueskytechco\Themeoption\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
	
	public function isLoggedIn(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create('Magento\Customer\Model\Session');
		if ($customerSession->isLoggedIn()) {
			return true;
		}
		return false;
	}
	
	public function getBaseUrl($url){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getBaseUrl().$url;
	}
	
	public function getPriceDisplayCustom($html) {
        return preg_replace('/(<[^>]+) id=".*?"/i', '$1', $html);
    }
}
