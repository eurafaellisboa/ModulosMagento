<?php
namespace MGS\Ajaxlayernavigation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) { 
        $this->_priceCurrency = $priceCurrency;  
        parent::__construct($context);
    }

    public function getPriceCurrencyPos()
    {
        $currency = $this->_priceCurrency->getCurrency();
        $symbol = $currency->getCurrencySymbol();
        $tmpPrice = $currency->format(0);
        if (strpos($symbol, $tmpPrice) > 0) {
            $prefix = '';
            $postfix = ' '.$symbol;
        } else {
            $prefix = $symbol;
            $postfix = '';
        }

        return ['prefix' => $prefix, 'postfix' => $postfix];
    }

    public function getClearAllUrl()
    {
        $searchQuery = $this->_getRequest()->getParam('q');
        $viewMode = $this->_getRequest()->getParam('product_list_mode');
        $query = [
            '_' => null
        ];

        if ($this->iaAjaxEnable()) {
            $query['is_ajax'] = 1;
        }
        if ($searchQuery) {
            $query['q'] = $searchQuery;
        }
		
        if ($viewMode) {
            $query['product_list_mode'] = $viewMode;
        }

        return $this->_urlBuilder->getUrl('*/*/*',
            [
                '_use_rewrite' => true,
                '_query' => $query
            ]
        );
    }
    public function iaAjaxEnable()
    {
        return $this->scopeConfig->getValue(
            "mgs_ajaxnavigation/general/enable", ScopeInterface::SCOPE_STORE);
    }

    public function usePriceSlide(){
        return $this->scopeConfig->getValue(
            "mgs_ajaxnavigation/general/range_price", ScopeInterface::SCOPE_STORE);
    }
	
	public function removeShoppingBy(){
		return $this->scopeConfig->getValue(
            "mgs_ajaxnavigation/general/shopping_by", ScopeInterface::SCOPE_STORE);
	}
}
