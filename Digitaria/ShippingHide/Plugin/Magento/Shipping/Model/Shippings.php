<?php
namespace Digitaria\ShippingHide\Plugin\Magento\Shipping\Model;
 
class Shipping
{
    protected $product;
 
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $product
    ) {
        $this->product = $product; 
    }
 
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        $noFreeShipping = false;
        $allItems = $request->getAllItems();
         
        // iterate all cart products to check if no_free_shipping is true
        foreach ($allItems as $item) {    
            $_product = $this->product->create()->load($item->getProduct()->getId());
            // if product has no_free_shipping true
			
            if ($_product->getData('ad_productfreeshipping') === "0") {
                $noFreeShipping = true;
                break;
            }
        }
        // if no_free_shipping is yes and shipping method free shipping return nothing
        if ($noFreeShipping && $carrierCode == 'freeshipping') {
            return false;
        }
 
        $result = $proceed($carrierCode, $request);
        return $result;
    }
}