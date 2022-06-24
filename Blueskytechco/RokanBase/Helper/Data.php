<?php
namespace Blueskytechco\RokanBase\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
    }
    
    public function getConfigData($path)
    {
        $value = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $value;
    }
    public function getSalableQuantityDataBySku($sku) {
        $qty = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $StockState = $objectManager->get('\Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku');
        $qty_arr = $StockState->execute($sku);
        if(is_array($qty_arr) && count($qty_arr) > 0){
            foreach ($qty_arr as $key => $value) {
                if(isset($value['qty'])){
                    $qty += $value['qty'];
                }
            }
        }
        return $qty;
    }
}
