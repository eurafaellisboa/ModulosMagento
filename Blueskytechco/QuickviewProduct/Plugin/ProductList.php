<?php
namespace Blueskytechco\QuickviewProduct\Plugin;

use Blueskytechco\QuickviewProduct\Helper\Data as Helper;

class ProductList
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Helper $helper
    ) {
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    public function aroundGetProductDetailsHtml(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        $enabled = $this->helper->getData('quickview_product/general/enabled');
        if ($enabled) {
            return $result . '<div class="blueskytechco-quickview-id" data-id="'.$product->getId().'"></div>';
        }
        
        return $result;
    }
}
