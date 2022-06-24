<?php
namespace Blueskytechco\SearchSuite\Helper;

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

    public function getSuggestProductUrl()
    {
        $enable = $this->getConfigData('searchsuite/product_suggestion/enable');
        $cate_id = $this->getConfigData('searchsuite/product_suggestion/category');
        if ($enable && $cate_id) {
            return $this->_getUrl(
                'saerch_suite/search_ajax/suggestproduct',
                ['_query' => ['cate' => $cate_id]]
            );
        } 
        return false;
    }
}
