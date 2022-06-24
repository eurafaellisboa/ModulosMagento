<?php
 
namespace Blueskytechco\Themeoption\Plugin\Result;

use Magento\Framework\App\ResponseInterface;
 
class Page
{    
    private $_context;

    protected $_helperProduct;
    protected $_themeConfig;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Blueskytechco\Themeoption\Helper\Product $helperProduct,
        \Blueskytechco\Themeoption\Helper\Themeconfig $themeConfig
    ) {
        $this->_context = $context;
        $this->_helperProduct = $helperProduct;
        $this->_themeConfig = $themeConfig;
    }

    public function beforeRenderResult(\Magento\Framework\View\Result\Page $subject, ResponseInterface $response)
    {
        $subject->getConfig()->addBodyClass($this->_helperProduct->getCustomClassStyleProduct());
        if(!$this->_helperProduct->getConfiguration('themesetting/category/show_product_rating_stars')){
            $subject->getConfig()->addBodyClass('hide__rating');
        }
        if(!$this->_helperProduct->getConfiguration('themesetting/category/show_product_price')){
            $subject->getConfig()->addBodyClass('hide__price');
        }
        if(!$this->_helperProduct->getConfiguration('themesetting/category/show_product_quickview')){
            $subject->getConfig()->addBodyClass('hide__quickview');
        }
        if(!$this->_helperProduct->getConfiguration('themesetting/category/show_product_compare')){
            $subject->getConfig()->addBodyClass('hide__compare');
        }
        if(!$this->_helperProduct->getConfiguration('themesetting/category/show_product_wishlist')){
            $subject->getConfig()->addBodyClass('hide__wishlist');
        }
        if(!$this->_helperProduct->getConfiguration('themesetting/category/show_product_add_to_cart')){
            $subject->getConfig()->addBodyClass('hide__addtocart');
        }
        if($this->_themeConfig->isEnableStickyHeader()){
            $subject->getConfig()->addBodyClass('enable__sticky--header');
        }
        if($this->_helperProduct->getConfiguration('themesetting/product/disable_add_to_cart_related')){
            $subject->getConfig()->addBodyClass('hide__addtocart--related');
        }
        if($this->_helperProduct->getConfiguration('themesetting/header/disable_store_view_topbar')){
            $subject->getConfig()->addBodyClass('hide__storeview--topbar');
        }
        return [$response];
    }    
}