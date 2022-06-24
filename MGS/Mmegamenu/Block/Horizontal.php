<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mmegamenu\Block;

/**
 * Main contact form block
 */
class Horizontal extends Abstractmenu
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
}

