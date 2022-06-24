<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Blueskytechco\QuickviewProduct\ViewModel;

use Blueskytechco\QuickviewProduct\Helper\Data as Helper;
use Magento\Store\Model\StoreManagerInterface;

class QuickView implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Helper $helper
    ) {
        $this->storeManager = $storeManager;
        $this->helper = $helper;
    }
    
    /**
     * return data config Admin
     */
    public function getData($config)
    {
        $config = $this->helper->getData('quickview_product/general/'.$config);
        return $config;
    }
}
