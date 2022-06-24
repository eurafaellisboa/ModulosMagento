<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace MGS\Portfolio\Model;

class Category extends \Magento\Framework\Model\AbstractModel
{
   
    /**
     * Initialize resource model
     *
     * @return void
     */
	 
	 protected $_stores = [];
	 
	 
    protected function _construct()
    {
        $this->_init('MGS\Portfolio\Model\ResourceModel\Category');
    }
	public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = $this->_getResource()->getStores($this);
        }

        return $this->_stores;
    }
}
