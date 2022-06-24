<?php
namespace MGS\AjaxCart\Helper;

use MGS\AjaxCart\Model\Source\AnimationType as AnimationType;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    // Define variable global
    const XML_PATH_ENABLE = 'ajaxcart/general/enable';
    const PRODUCT_TYPE_SIMPLE = 'simple';
    const XML_PATH_ANIMATION_TYPE = 'ajaxcart/additional/animation_type';

    /**
     * @var MGS\AjaxCart\Helper\Data
     */
    protected $aHelper;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    
    protected $_storeManager;
    /**
     * [__construct description]
     * @param \Magento\Framework\App\Helper\Context $context[description]
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $storeManager;
    }


    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /*
     * return enable / disable module with magento path
     * @return string
     */
    public function isEnable()
    {
        return $this->getConfig(self::XML_PATH_ENABLE);

    }

    /*
     * return message with magento path
     * @return string
     */
    public function getConfig($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public static function getConfigStatic($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    /**
     * Check type product
     * @return boolean
     */
    public function checkTypeProduct($product)
    {
        if($product->getTypeId() == self::PRODUCT_TYPE_SIMPLE) {
            return true;
        }
        return false;
    }

    /**
     * Check isSiderbarCart
     * @return boolean
     */
    public function checkSiderbarCart()
    {
        if($this->getConfig(self::XML_PATH_ANIMATION_TYPE) == AnimationType::TYPE_SIDEBAR_CART) {
            return true;
        }
        return false;
    }

    /**
     * Check apply type ajaxcart for product.
     * @return boolean
     */
    public function checkApplySidebarCart($product)
    {
        if($this->checkSiderbarCart() && $this->checkTypeProduct($product) == false) {
            return true;
        }
        return false;
    }

    /**
     * @param $product
     * @param $activeFilters
     * @param $attribute_code
     * @return bool
     */
    public function checkIfAttributeIsPresent($product, $activeFilters, $attribute_code) {
        $values = [];
	foreach ($activeFilters as $filter) {
            $attributeData = $filter->getFilter()->getAttributeModel()->getData();
            if ($attributeData['attribute_code'] === $attribute_code) {
                $values[] = $filter->getData('value');
            }
        }

        if(empty($values)) {
            return true;
        }

        $childProducts = $product->getTypeInstance()->getUsedProducts($product);
        $hasTamanhoAttribute = false;
        foreach ($childProducts as $child) {
            if (in_array($child->getData('tamanho'), $values)) {
                $hasTamanhoAttribute = true;
            }
        }
        return $hasTamanhoAttribute;
    }
    
}
