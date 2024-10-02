<?php
namespace Digitaria\ShippingHide\Plugin\Magento\Shipping\Model;
 
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Model\Quote;
use Digitaria\ShippingHide\Plugin\Magento\Catalog\CurrentProduct;
use Magento\Framework\UrlInterface;

class Shipping
{
    private $currentProduct;
    private $urlBuilder;
    private $scopeConfig;
    protected $cart;
    protected $productRepository;   
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        CurrentProduct $currentProduct,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->currentProduct = $currentProduct;
        $this->urlBuilder = $urlBuilder;
    }
 
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
		 
		 
        $noFreeShipping = false;
        $allItems = [];
        $cartValue = $this->cart->getQuote()->getSubtotal();
        $freeShippingValue = $this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        
        if (strpos($currentUrl, 'cart') !== false || strpos($currentUrl, 'checkout') !== false) {
            $allItems = $request->getAllItems();
			   
           
            foreach ($allItems as $item) {
                $_product = $this->productRepository->getById($item->getProduct()->getId());
                
                if ($_product->getData('ad_productfreeshipping') === "0" || $cartValue < $freeShippingValue) {
                    $noFreeShipping = true;
                    break;
                }
            }
        } else {
			   $product = '';
            $product = $this->currentProduct->getProduct();
            $productValue = $product->getFinalPrice();
            
            if ($product->getData('ad_productfreeshipping') === "0" || $productValue < $freeShippingValue) {
                $noFreeShipping = true;
            }
        }
       
        // Verificações relacionadas às faixas de CEP
        $postcode = $request->getDestPostcode();
        $postcode = str_replace('-', '', $postcode);
        $zipRanges = $this->scopeConfig->getValue('shippinghide/general/zip_ranges');
        $intervalos = $this->parseZipRanges($zipRanges);
       
        if (!checkZipRange($postcode, $intervalos)) {
            $noFreeShipping = true;
        }
       
        if ($noFreeShipping && $carrierCode == 'freeshipping') {
            return false;
        }
 
        $result = $proceed($carrierCode, $request);
        return $result;
    }
    
    private function parseZipRanges($zipRanges)
    {
        $lines = explode(PHP_EOL, $zipRanges);
        $intervalos = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (!empty($line)) {
                $range = explode(',', $line);
                
                if (count($range) === 2) {
                    $min = trim($range[0]);
                    $max = trim($range[1]);
                    $intervalos[] = [$min, $max];
                }
            }
        }
        
        return $intervalos;
    }
}

function checkZipRange($postcode, $intervalos)
{
    $v = (int) preg_replace("/\D+/", "", $postcode);

    // Ordenar as faixas de CEP em ordem crescente
    usort($intervalos, function($a, $b) {
        return $a[0] <=> $b[0];
    });

    // Busca binária para encontrar a faixa correta
    $left = 0;
    $right = count($intervalos) - 1;

    while ($left <= $right) {
        $mid = floor(($left + $right) / 2);
        $range = $intervalos[$mid];

        if ($v >= $range[0] && $v <= $range[1]) {
            return true;
        }

        if ($v < $range[0]) {
            $right = $mid - 1;
        } else {
            $left = $mid + 1;
        }
    }

    return false;
}
