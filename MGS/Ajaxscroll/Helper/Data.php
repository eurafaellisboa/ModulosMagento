<?php

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Ajaxscroll\Helper;

/**
 * Contact base helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_storeManager;
	
	protected $scopeConfig;

	
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\Element\Context $context
	){
		$this->scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
    }
	
	public function getStoreConfig($node){
        return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function getMediaUrl(){
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
	}
	
	public function getLoadingImageUrl($defaultImage){
		$loadingImage = $this->getStoreConfig('ajaxscroll/general/image');

		if($loadingImage!=''){
			$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			return $mediaUrl.'ajaxscroll/'.$loadingImage;
		}else{
			return $defaultImage;
		}
		
	}
}