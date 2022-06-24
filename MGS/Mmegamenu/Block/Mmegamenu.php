<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mmegamenu\Block;

/**
 * Main contact form block
 */
class Mmegamenu extends Abstractmenu
{
	public function getMegamenuItems(){
		$store = $this->getStore();
		$menuCollection = $this->getModel('MGS\Mmegamenu\Model\Mmegamenu')
			->getCollection()
			->addStoreFilter($store)
			->addFieldToFilter('parent_id', 1)
			->addFieldToFilter('status', 1)
			->setOrder('position', 'ASC')
		;
		return $menuCollection;
	}
	
	public function getMegamenuItemsContent(){
		$storeId = $this->getStore()->getId();
		$contentCollection = $this->getModel('MGS\Mmegamenu\Model\Cache')
			->getCollection()
			->addFieldToFilter('parent_menu_id', 1)
			->addFieldToFilter('store_id', $storeId)
		;
		
		$_contentResult = array();
		foreach($contentCollection as $_content){
			$_contentResult['id-'.$_content->getParentId()] = $_content;
		}
		return $_contentResult;
	}
}

