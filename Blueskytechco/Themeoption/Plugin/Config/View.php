<?php
 
namespace Blueskytechco\Themeoption\Plugin\Config;
 
class View
{    
    protected $_scopeConfig;
    protected $_helperProduct;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Blueskytechco\Themeoption\Helper\Product $helperProduct
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_helperProduct = $helperProduct;
    }

    public function afterGetMediaAttributes(\Magento\Framework\Config\View $subject, $result, $module, $mediaType, $mediaId)
    {
        $process_result = $result;
        if($module == 'Magento_Catalog' && $mediaType == 'images'){
            $process_result = $this->_helperProduct->getConfigurationSizeImages($mediaId, $process_result);
        }
       
        return $process_result;
    }    
}