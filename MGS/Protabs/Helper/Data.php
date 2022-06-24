<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Protabs\Helper;

/**
 * Contact base helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_storeManager;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	protected $_attributeCollection;
	protected $_scopeConfig;
	
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollection
	){
		$this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
		$this->_objectManager = $objectManager;
		$this->_attributeCollection = $attributeCollection;
    }
	
	public function getStore(){
        return $this->_storeManager->getStore();
    }
     
    public function getModel($model){
        return $this->_objectManager->create($model);
    }
     
    /* Get system store config */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->_scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->_scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
	public function getAttributeCollection(){
		return $this->_attributeCollection->create()->addVisibleFilter();
	}
	
	public function getTabsCollection(){
		$storeId = $this->getStore()->getId();
		$websiteId = $this->getStore()->getWebsiteId();
		$collection = $this->getModel('MGS\Protabs\Model\Protabs')->getCollection()->addFieldToFilter('scope', 'stores')->addFieldToFilter('scope_id', $storeId)->setOrder('position', 'ASC');
		
		if($collection->getSize()==0){
			$collection  = $this->getModel('MGS\Protabs\Model\Protabs')->getCollection()->addFieldToFilter('scope', 'websites')->addFieldToFilter('scope_id', $websiteId)->setOrder('position', 'ASC');
			
			if($collection->getSize()==0){
				$collection  = $this->getModel('MGS\Protabs\Model\Protabs')->getCollection()->addFieldToFilter('scope', 'default')->setOrder('position', 'ASC');
			}
		}
		return $collection;
	}
	
	public function convertAttributeToCallName($attributeCode){
		$arrText = explode("_", $attributeCode);
		$result = 'get';
		if(count($arrText)>1){
			foreach($arrText as $_text){
				$result.=ucfirst($_text);
			}
		}else{
			$result.=ucfirst($arrText[0]);
		}
		return $result;
	}
	
	public function getAttributeType($attributeCode){
		$attribute = $this->getAttributeCollection()->addFieldToFilter('attribute_code', $attributeCode)->getFirstItem();
		if($attribute->getFrontendInput() == 'select'){
			return 'text';
		}
		if($attribute->getFrontendInput() == 'multiselect'){
			return 'list';
		}
		return 'none';
	}
}