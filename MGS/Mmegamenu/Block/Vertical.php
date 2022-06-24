<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mmegamenu\Block;

/**
 * Main contact form block
 */
class Vertical extends Abstractmenu
{
	public function getMegamenuItems(){
		$store = $this->getStore();
		$menuCollection = $this->getModel('MGS\Mmegamenu\Model\Mmegamenu')
			->getCollection()
			->addStoreFilter($store)
			->addFieldToFilter('parent_id', $this->getMenuId())
			->addFieldToFilter('status', 1)
			->setOrder('position', 'ASC')
		;
		return $menuCollection;
	}
	
	public function getCustomClass(){
		$menuId = $this->getMenuId();
		$class = '';
		
		$menuCollection = $this->getModel('MGS\Mmegamenu\Model\Parents')
			->getCollection()
			->addFieldToFilter('parent_id', $menuId)->getFirstItem();
		;
		if($menuCollection){
			$class = $menuCollection->getCustomClass();
		}
		
		return $class;
	}
}

