<?php
namespace Blueskytechco\SetProduct\Block\Adminhtml\Field;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Model\Attribute;
use Blueskytechco\OnePageCheckout\Helper\Address;
use Magento\Framework\Registry;

class Details extends Template
{
    protected $_template = 'Blueskytechco_SetProduct::field/details.phtml';
    protected $_coreRegistry;
    protected $_productloader;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $productloader,
        array $data = []
    ) {
        $this->_productloader = $productloader;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    public function getLookbookInfo()
    {
        return $this->_coreRegistry->registry('blueskytechco_setproduct');
    }
    public function getWidth()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $w = 1000;
        if($lookbook->getWidth()){
            $w = $lookbook->getWidth();
        }
        return $w;
    }
    public function getHeight()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $h = 1000;
        if($lookbook->getHeight()){
            $h = $lookbook->getHeight();
        }
        return $h;
    }
    public function getBannerImage()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $b = '';
        if($lookbook->getBannerImage()){
            if (strpos($lookbook->getBannerImage(), 'placehold.jp') === false) {
                $trim_pub = explode("/", $lookbook->getBannerImage());
                $b = end($trim_pub);
            }
        }
        return $b;
    }
    public function getName()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $t = '';
        if($lookbook->getName()){
            $t = $lookbook->getName();
        }
        return $t;
    }
    public function getTitle()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $t = '';
        if($lookbook->getTitle()){
            $t = $lookbook->getTitle();
        }
        return $t;
    }
    public function getTitleLink()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $t = '';
        if($lookbook->getTitleLink()){
            $t = $lookbook->getTitleLink();
        }
        return $t;
    }
    public function getButtonStyle()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $t = '';
        if($lookbook->getButtonStyle()){
            $t = $lookbook->getButtonStyle();
        }
        $html = '';
        $arr = ['dark' => __('Dark'), 'light' => __('Light')];
        foreach($arr as $k => $a){
            if($k == $t){
                $html .= '<option value="'.$k.'" selected>'.$a.'</option>';
            }
            else{
                $html .= '<option value="'.$k.'">'.$a.'</option>';
            }
        }
        return $html;
    }
    public function getBannerImageSrc()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $t = '//placehold.jp/1aada3/fff/1000x1000.png?text=Image';
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if($image = $lookbook->getBannerImage()){
            if (strpos($image, 'placehold.jp') === false) {
                $t = $mediaUrl . $image;
            }
            else{
                $t = $image;
            }
        }
        return $t;
    }
    public function getPin()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $html = '';
        
        if($lookbook->getProductData() && $lookbook->getProductData() != ''){
            $pro_decode = json_decode($lookbook->getProductData(), true);
            foreach ($pro_decode as $key_pro => $val_pro) {
                $html .= '<div data-pin="'.$key_pro.'" class="lookbook-icon '.$lookbook->getButtonStyle().'" id="pin_'.$key_pro.'" style="top: '.$val_pro['top'].'%; left: '.$val_pro['left'].'%;"><a href="#" rel="nofollow" class="pin-icon-product"><span class="pin-content-product">'.$val_pro['label'].'</span></a></div>';
            }
        }
        
        return $html;
    }
    public function getTitleLinkHtml()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $html = '<a href="'.$lookbook->getTitleLink().'">'.$lookbook->getTitle().'</a>';
        
        return $html;
    }
    public function getListProducts()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $html = '';
        
        if($lookbook->getProductData() && $lookbook->getProductData() != ''){
            $pro_decode = json_decode($lookbook->getProductData(), true);
            foreach ($pro_decode as $key_pro => $val_pro) {
                $pr_sku = '';
                if(isset($val_pro['product_id'])){
                    $product = $this->_productloader->create()->load($val_pro['product_id']);
                    $pr_sku = '<option value="'.$val_pro['product_id'].'" selected>'.$product->getSku().'</option>';
                }
                
                $html .= '<li id="lookbook_'.$key_pro.'" data-id="'.$key_pro.'"><div class="div-container-select-product"><select class="js-data-products-ajax" name="products['.$key_pro.'][product_id]">'.$pr_sku.'</select></div><div class="div-container-add-label"><input name="products['.$key_pro.'][label]" value="'.htmlentities($val_pro['label'], ENT_QUOTES).'" type="text" class="input-text icon-label-keyup-event" placeholder="'.__('Add a lable').'"></div><div class="div-container-del-product"><button type="button" title="'.__('Delete Product').'" class="delete-product-lookbook"><i class="fas fa-trash"></i></button><input name="products['.$key_pro.'][top]" class="pin-top" type="hidden" value="'.$val_pro['top'].'" /><input name="products['.$key_pro.'][left]" class="pin-left" type="hidden" value="'.$val_pro['left'].'" /></div></li>';
            }
        }
        
        return $html;
    }
    public function getEntityId()
    {
        $lookbook = $this->_coreRegistry->registry('blueskytechco_setproduct');
        $html = '';
        
        if($lookbook->getId()){
            $html = '<input name="entity_id" type="hidden" value="'.$lookbook->getId().'">';
        }
        
        return $html;
    }
}