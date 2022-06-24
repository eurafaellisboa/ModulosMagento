<?php

namespace Blueskytechco\Themeoption\Model\Custom;

class Generator
{
	protected $_configData;
    protected $_coreRegistry;
    protected $_storeManager;
    protected $_layoutManager;
    
    public function __construct(
        \Blueskytechco\Themeoption\Helper\Themeconfig $configData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layoutManager
    ) {
        $this->_configData = $configData;
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_layoutManager = $layoutManager;
    }
    
    public function generateCss($websiteId, $storeId){
        if(!$websiteId && !$storeId) {
            $websites = $this->_storeManager->getWebsites(false, false);
            foreach ($websites as $id => $value) {
                $this->generateWebsiteCustomTheme($id);
            }
        } else {
            if($storeId) {
                $this->generateStoreCustomTheme($storeId);
            } else {
                $this->generateWebsiteCustomTheme($websiteId);
            }
        }        
    }
    
    protected function generateWebsiteCustomTheme($websiteId) {
        $website = $this->_storeManager->getWebsite($websiteId);
        foreach($website->getStoreIds() as $storeId){
            $this->generateStoreCustomTheme($storeId);
        }
    }

    protected function minimizeCSSsimple($css){
        $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css);
        $css = preg_replace('/\s{2,}/', ' ', $css);
        $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
        $css = preg_replace('/;}/', '}', $css);
        return $css;
    }

    protected function generateStoreCustomTheme($storeId) {
        $store = $this->_storeManager->getStore($storeId);
        if(!$store->isActive())
            return;
        $storeCode = $store->getCode();
        $str1 = 'store_'.$storeCode;
        $str2 = $str1.'.css';
        if(isset($_POST['groups']['general']['fields']['debug_mode']['value']) && $_POST['groups']['general']['fields']['debug_mode']['value'] == 1){
            $str2 = $str1.'.min.css';
        }
        $str3 = $this->_configData->getConfigDir().$str2;
        $this->_coreRegistry->register('css_store', $storeCode);

        try {
            $block = $this->_layoutManager->createBlock('Blueskytechco\Themeoption\Block\Customcss')->setData('area','frontend')->setTemplate('css/theme_option.phtml')->toHtml();
            
            if(isset($_POST['groups']['general']['fields']['debug_mode']['value']) && $_POST['groups']['general']['fields']['debug_mode']['value'] == 1){
                $block = $this->minimizeCSSsimple($block);
            }
            if(!file_exists($this->_configData->getConfigDir())) {
                @mkdir($this->_configData->getConfigDir(), 0777);
            }
            $file = @fopen($str3,"w+");
            @flock($file, LOCK_EX);
            @fwrite($file,$block);
            @flock($file, LOCK_UN);
            @fclose($file);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        $this->_coreRegistry->unregister('css_store');
    }
}
