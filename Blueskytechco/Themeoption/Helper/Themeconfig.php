<?php

namespace Blueskytechco\Themeoption\Helper;

class Themeconfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $cssFolder;
    protected $cssPath;
    protected $cssDir;
	protected $_themeoptionversion;
	protected $_appState;
	protected $_request;
    
    public function __construct(
    	\Magento\Framework\App\Request\Http $request,
    	\Magento\Framework\App\State $appState,
        \Magento\Framework\App\Helper\Context $context,
		\Blueskytechco\Themeoption\Model\Themeoptionversion $themeoptionversion,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    	$this->_request = $request;
    	$this->_appState = $appState;
        $this->_storeManager = $storeManager;
        $base = BP;
        $this->cssFolder = 'blueskytechco/theme_option/';
        $this->cssPath = 'pub/media/'.$this->cssFolder;
        $this->cssDir = $base.'/'.$this->cssPath;
		$this->_themeoptionversion = $themeoptionversion;
        parent::__construct($context);
    }

    public function getConfiguration($path, $storeId = null)
	{
		return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
	}

	public function isEnableTopBar(){
        return $this->getConfiguration('themesetting/header/enable_topbar');
	}

	public function isEnableCustomMenu($data){
		if($this->issetDataCustom($data, 'magemenu', 'menu_custom')){
    		return true;
    	}
        return false;
	}

	public function getCustomMenuColorCode($data, $color){
		if($this->issetDataCustom($data, 'magemenu', $color) != '' && $this->isEnableCustomMenu($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'magemenu', $color));
    	}
        return '';
	}

	public function getButtonBorderRadius($data){
		if($this->issetDataCustom($data, 'general', 'button_border_radius') != ''){
			if($this->issetDataCustom($data, 'general', 'button_border_radius') == '0' || $this->issetDataCustom($data, 'general', 'button_border_radius') == 0 || $this->issetDataCustom($data, 'general', 'button_border_radius') == '0px' || $this->issetDataCustom($data, 'general', 'button_border_radius') == '0%'){
				return $this->issetDataCustom($data, 'general', 'button_border_radius').'px';
			}
    		return $this->issetDataCustom($data, 'general', 'button_border_radius');
    	}
        return '';
	}

	public function getCustomizationStyle($data){
		if($this->issetDataCustom($data, 'customization', 'custom_style')){
    		return $this->issetDataCustom($data, 'customization', 'custom_style');
    	}
        return false;
	}

	public function isEnableMainContentContainer($data){
		if($this->issetDataCustom($data, 'main', 'main_content_container_custom')){
    		return true;
    	}
        return false;
	}

	public function getMainContentContainerBackgroundColor($data){
		if($this->issetDataCustom($data, 'main', 'main_bgcolor') != '' && $this->isEnableMainContentContainer($data)){
    		return $this->formatBgColor($this->issetDataCustom($data, 'main', 'main_bgcolor'));
    	}
        return '';
	}

	public function getUrlMediaReplaceSSL(){
		return str_replace("https://","//",str_replace("http://","//",$this->getBaseMediaUrl()));
	}

	public function getMainContentContainerBackgroundImage($data){
		if($this->issetDataCustom($data, 'main', 'main_bg_image') != '' && $this->isEnableMainContentContainer($data)){
    		$folderName = \Blueskytechco\Themeoption\Model\Config\Mainbackground::UPLOAD_DIR;
			$imageUrl = $this->getUrlMediaReplaceSSL().$folderName . '/' . $this->issetDataCustom($data, 'main', 'main_bg_image');
			$style = 'background-image: url("'.$imageUrl.'");'.$this->issetDataCustom($data, 'main', 'main_custom_style');
    		return $style;
    	}
        return '';
	}

	public function isEnablePageWrapper($data){
		if($this->issetDataCustom($data, 'page', 'page_wrapper_custom')){
    		return true;
    	}
        return false;
	}

	public function getPageWrapperBackgroundColor($data){
		if($this->issetDataCustom($data, 'page', 'page_bgcolor') != '' && $this->isEnablePageWrapper($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'page', 'page_bgcolor'));
    	}
        return '';
	}

	public function getPageWrapperBackgroundImage($data){
		if($this->issetDataCustom($data, 'page', 'page_bg_image') != '' && $this->isEnablePageWrapper($data)){
    		$folderName = \Blueskytechco\Themeoption\Model\Config\Pagebackground::UPLOAD_DIR;
			$imageUrl = $this->getUrlMediaReplaceSSL().$folderName . '/' . $this->issetDataCustom($data, 'page', 'page_bg_image');
			$style = 'background-image: url("'.$imageUrl.'");'.$this->issetDataCustom($data, 'page', 'page_custom_style');
    		return $style;
    	}
        return '';
	}

	public function isEnableCustomFooter($data){
		if($this->issetDataCustom($data, 'footer', 'footer_custom')){
    		return true;
    	}
        return false;
	}

	public function getCustomFooterColor($data){
		if($this->issetDataCustom($data, 'footer', 'footer_text_color') != '' && $this->isEnableCustomFooter($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'footer', 'footer_text_color'));
    	}
        return '';
	}

	public function getFooterBackgroundColor($data){
		if($this->issetDataCustom($data, 'footer', 'footer_background_color') != '' && $this->isEnableCustomFooter($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'footer', 'footer_background_color'));
    	}
        return '';
	}

	public function getFooterBackgroundImage($data){
		if($this->issetDataCustom($data, 'footer', 'footer_bg_image') != '' && $this->isEnableCustomFooter($data)){
    		$folderName = \Blueskytechco\Themeoption\Model\Config\Footerbackground::UPLOAD_DIR;
			$imageUrl = $this->getUrlMediaReplaceSSL().$folderName . '/' . $this->issetDataCustom($data, 'footer', 'footer_bg_image');
			$style = 'background-image: url("'.$imageUrl.'");'.$this->issetDataCustom($data, 'footer', 'footer_custom_style');
    		return $style;
    	}
        return '';
	}

	public function isEnableCustomHeader($data){
		if($this->issetDataCustom($data, 'header', 'header_custom')){
    		return true;
    	}
        return false;
	}

	public function getTopbarTextColor($data){
		if($this->issetDataCustom($data, 'header', 'header_topbar_text_color') != '' && $this->isEnableCustomHeader($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'header', 'header_topbar_text_color'));
    	}
        return '';
	}

	public function getTopbarBackgroundColor($data){
		if($this->issetDataCustom($data, 'header', 'header_topbar_background_color') != '' && $this->isEnableCustomHeader($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'header', 'header_topbar_background_color'));
    	}
        return '';
	}

	public function getHeaderTextColor($data){
		if($this->issetDataCustom($data, 'header', 'header_text_color') != '' && $this->isEnableCustomHeader($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'header', 'header_text_color'));
    	}
        return '';
	}

	public function getHeaderBackgroundColor($data){
		if($this->issetDataCustom($data, 'header', 'header_background_color') != '' && $this->isEnableCustomHeader($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'header', 'header_background_color'));
    	}
        return '';
	}

	public function getHeaderBackgroundImage($data){
		if($this->issetDataCustom($data, 'header', 'header_bg_image') != '' && $this->isEnableCustomHeader($data)){
    		$folderName = \Blueskytechco\Themeoption\Model\Config\Headerbackground::UPLOAD_DIR;
			$imageUrl = $this->getUrlMediaReplaceSSL().$folderName . '/' . $this->issetDataCustom($data, 'header', 'header_bg_image');
			$style = 'background-image: url("'.$imageUrl.'");'.$this->issetDataCustom($data, 'header', 'header_custom_style');
    		return $style;
    	}
        return '';
	}

	public function isEnableBasicColors($data){
		if($this->issetDataCustom($data, 'colors', 'base_color_custom')){
    		return true;
    	}
        return false;
	}

	public function isEnableButtonsColors($data){
		if($this->issetDataCustom($data, 'colors', 'button_color_custom')){
    		return true;
    	}
        return false;
	}

	public function getCustomStickyBackgroundColor($data){
		if($this->issetDataCustom($data, 'header', 'sticky_select_bg_color') == 'custom' && $this->issetDataCustom($data, 'header', 'sticky_bg_color_custom') != ''){
    		return $this->getCodeColor($this->issetDataCustom($data, 'header', 'sticky_bg_color_custom'));
    	}
        return false;
	}

	public function getButtonsColors($data, $color){
		if($this->issetDataCustom($data, 'colors', $color) != '' && $this->isEnableButtonsColors($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'colors', $color));
    	}
        return '';
	}

	public function formatBgColor($color)
	{
		if (strlen($color) == 6){
			return 'background-color: #'.$color.';';
		}
		return 'background-color: '.$color.';';
	}

	public function formatColor($color)
	{
		if (strlen($color) == 6){
			return 'color: #'.$color.';';
		}
		return 'color: '.$color.';';
	}

	public function getCodeColor($color)
	{
		if (strlen($color) == 6){
			return '#'.$color;
		}
		return $color;
	}

	public function getBasicColors($data, $color){
		if($this->issetDataCustom($data, 'colors', $color) != '' && $this->isEnableBasicColors($data)){
    		return $this->getCodeColor($this->issetDataCustom($data, 'colors', $color));
    	}
        return '';
	}

	public function getThemeColors($data){
		if($this->issetDataCustom($data, 'colors', 'theme_colors') != ''){
    		return $this->getCodeColor($this->issetDataCustom($data, 'colors', 'theme_colors'));
    	}
        return '';
	}

	public function getIconLazyLoad($data){
		if($this->issetDataCustom($data, 'general', 'icon_lazyload') != ''){
			$folderName = \Blueskytechco\Themeoption\Model\Config\IconlazyLoad::UPLOAD_DIR;
			$imageUrl = $this->getUrlMediaReplaceSSL().$folderName . '/' . $this->issetDataCustom($data, 'general', 'icon_lazyload');
			$style = 'background-image: url("'.$imageUrl.'");';
    		return $style;
    	}
        return '';
	}

	public function getPageWidth($data){
		if($this->issetDataCustom($data, 'general', 'layout') != '' && $this->issetDataCustom($data, 'general', 'layout') != 'box-layout'){
    		return $this->issetDataCustom($data, 'general', 'layout');
    	}
        return '';
	}
	public function isBoxLayout($data){
		if($this->issetDataCustom($data, 'general', 'layout') == 'box-layout'){
    		return true;
    	}
        return false;
	}
	
	public function issetDataCustom($data, $key1, $key2)
	{
		if(isset($data[$key1][$key2]) && $data[$key1][$key2] != ''){
			return $data[$key1][$key2];
		}
		return '';
	}

	public function getBodyFont($data, $type = 'family')
	{
		if($type == 'family'){
			if($this->issetDataCustom($data, 'font', 'font_family') == 'google' && $this->issetDataCustom($data, 'font', 'google_font_family') != ''){
				return "'".$this->issetDataCustom($data, 'font', 'google_font_family')."', sans-serif";
			}
			elseif($this->issetDataCustom($data, 'font', 'custom_font_family') != ''){
				return "'".$this->issetDataCustom($data, 'font', 'custom_font_family')."', sans-serif";
			}
		}
		if($type == 'size'){
			if($this->issetDataCustom($data, 'font', 'font_size') != ''){
				return $this->issetDataCustom($data, 'font', 'font_size');
			}
		}
		if($type == 'weight'){
			if($this->issetDataCustom($data, 'font', 'google_font_weight') != ''){
				return $this->issetDataCustom($data, 'font', 'google_font_weight');
			}
		}
		return '';
	}


	public function isCustomHeadingFont($data){
		if($this->issetDataCustom($data, 'font', 'heading_family') == 'google' || $this->issetDataCustom($data, 'font', 'heading_family') == 'custom'){
    		return true;
    	}
        return false;
	}

	public function getCustomHeadingFont($data, $type = 'family')
	{
		if($type == 'family'){
			if($this->issetDataCustom($data, 'font', 'heading_family') == 'google' && $this->issetDataCustom($data, 'font', 'google_heading_family') != ''){
				return "'".$this->issetDataCustom($data, 'font', 'google_heading_family')."', sans-serif";
			}
			elseif($this->issetDataCustom($data, 'font', 'custom_heading_family') != ''){
				return "'".$this->issetDataCustom($data, 'font', 'custom_heading_family')."', sans-serif";
			}
		}
		if($type == 'weight'){
			if($this->issetDataCustom($data, 'font', 'heading_font_weight') != ''){
				return $this->issetDataCustom($data, 'font', 'heading_font_weight');
			}
		}
		return '';
	}

	public function isCustomMenuFont($data){
		if($this->issetDataCustom($data, 'font', 'menu_family') == 'google' || $this->issetDataCustom($data, 'font', 'menu_family') == 'custom'){
    		return true;
    	}
        return false;
	}

	public function getCustomMenuFont($data, $type = 'family')
	{
		if($type == 'family'){
			if($this->issetDataCustom($data, 'font', 'menu_family') == 'google' && $this->issetDataCustom($data, 'font', 'google_menu_family') != ''){
				return "'".$this->issetDataCustom($data, 'font', 'google_menu_family')."', sans-serif";
			}
			elseif($this->issetDataCustom($data, 'font', 'custom_menu_family') != ''){
				return "'".$this->issetDataCustom($data, 'font', 'custom_menu_family')."', sans-serif";
			}
		}
		if($type == 'size'){
			if($this->issetDataCustom($data, 'font', 'menu_font_size') != ''){
				return $this->issetDataCustom($data, 'font', 'menu_font_size');
			}
		}
		if($type == 'weight'){
			if($this->issetDataCustom($data, 'font', 'menu_font_weight') != ''){
				return $this->issetDataCustom($data, 'font', 'menu_font_weight');
			}
		}
		return '';
	}

	public function isCustomOtherFont($data){
		if($this->issetDataCustom($data, 'font', 'other_family') == 'google' || $this->issetDataCustom($data, 'font', 'other_family') == 'custom'){
    		return true;
    	}
        return false;
	}

	public function getCustomOtherFont($data)
	{
		if($this->issetDataCustom($data, 'font', 'other_family') == 'google' && $this->issetDataCustom($data, 'font', 'google_other_family') != ''){
			return "'".$this->issetDataCustom($data, 'font', 'google_other_family')."', sans-serif";
		}
		elseif($this->issetDataCustom($data, 'font', 'custom_other_family') != ''){
			return "'".$this->issetDataCustom($data, 'font', 'custom_other_family')."', sans-serif";
		}
		return '';
	}

    public function useGoogleFonts($data)
    {
    	if($this->issetDataCustom($data, 'font', 'font_family') == 'google' || $this->issetDataCustom($data, 'font', 'heading_family') == 'google' || $this->issetDataCustom($data, 'font', 'menu_family') == 'google' || $this->issetDataCustom($data, 'font', 'other_family') == 'google'){
    		return true;
    	}
        return false;
    }

    public function getLinkGoogleFonts($data)
    {
    	$link = [];
    	$font_family = '';
    	$heading_family = '';
    	$menu_family = '';
    	$other_family = '';

    	if($this->issetDataCustom($data, 'font', 'font_family') == 'google'){
    		$font_family = 'family='.str_replace(" ","+", $this->issetDataCustom($data, 'font', 'google_font_family'));
    		if($this->issetDataCustom($data, 'font', 'customize') != ''){
	    		$font_family = $font_family.':'.$this->issetDataCustom($data, 'font', 'customize');
	    	}
	    	$link[] = $font_family;
    	}
    	if($this->issetDataCustom($data, 'font', 'heading_family') == 'google'){
    		$heading_family = 'family='.str_replace(" ","+", $this->issetDataCustom($data, 'font', 'google_heading_family'));
	    	if(strpos($font_family, $heading_family) === false){
	    		if($this->issetDataCustom($data, 'font', 'customize_heading_family') != ''){
		    		$heading_family = $heading_family.':'.$this->issetDataCustom($data, 'font', 'customize_heading_family');
		    	}
		    	$link[] = $heading_family;
		    }
    	}
    	if($this->issetDataCustom($data, 'font', 'menu_family') == 'google'){
    		$menu_family = 'family='.str_replace(" ","+", $this->issetDataCustom($data, 'font', 'google_menu_family'));
	    	if(strpos($font_family, $menu_family) === false && strpos($heading_family, $menu_family) === false){
	    		if($this->issetDataCustom($data, 'font', 'customize_menu_family') != ''){
		    		$menu_family = $menu_family.':'.$this->issetDataCustom($data, 'font', 'customize_menu_family');
		    	}
		    	$link[] = $menu_family;
		    }
    	}
    	if($this->issetDataCustom($data, 'font', 'other_family') == 'google'){
    		$other_family = 'family='.str_replace(" ","+", $this->issetDataCustom($data, 'font', 'google_other_family'));
	    	if(strpos($font_family, $other_family) === false && strpos($heading_family, $other_family) === false && strpos($menu_family, $other_family) === false){
	    		if($this->issetDataCustom($data, 'font', 'customize_other_family') != ''){
		    		$other_family = $other_family.':'.$this->issetDataCustom($data, 'font', 'customize_other_family');
		    	}
		    	$link[] = $other_family;
		    }
    	}
    	return implode("&", $link);
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    public function getConfigDir()
    {
        return $this->cssDir;
    }
    
    public function getThemeOption()
    {
		$getver = $this->_themeoptionversion->getLatestVersion();
		if($this->scopeConfig->getValue('themeoption/general/debug_mode') == 1){
			return $this->getBaseMediaUrl(). $this->cssFolder . 'store_' . $this->_storeManager->getStore()->getCode() . '.min.css?v='.$getver;
		}
        return $this->getBaseMediaUrl(). $this->cssFolder . 'store_' . $this->_storeManager->getStore()->getCode() . '.css?v='.$getver;
    }
	public function isEnableStickyHeader()
	{
		if($this->scopeConfig->getValue('themesetting/header/sticky_enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) == 'enable'){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function isEnableFakeOrder()
	{
		if($this->scopeConfig->getValue('themesetting/fake_order/enable_f_o', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function getInfoFakeOrder($path)
	{
		return $this->scopeConfig->getValue('themesetting/fake_order/'.$path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
	
	public function getFooterLogo()
	{
		$logo = $this->scopeConfig->getValue('themeoption/footer/footer_logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($logo != ''){
			$folderName = \Blueskytechco\Themeoption\Model\Config\Footerlogo::UPLOAD_DIR;
			$path = $folderName . '/' .$logo;
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$path;
		}
		else{
			return '';
		}
	}
	
	public function getStickyLogoHeader()
	{
		$logo = $this->scopeConfig->getValue('themeoption/header/sticky_logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($logo != ''){
			$folderName = \Blueskytechco\Themeoption\Model\Config\Stickylogo::UPLOAD_DIR;
			$path = $folderName . '/' .$logo;
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$path;
		}
		else{
			return '';
		}
	}
	
	public function getIconlazyLoadUrl()
	{
		$icon = $this->scopeConfig->getValue('themeoption/general/icon_lazyload', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($icon != ''){
			$folderName = \Blueskytechco\Themeoption\Model\Config\IconlazyLoad::UPLOAD_DIR;
			$path = $folderName . '/' .$icon;
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
			return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$path;
		}
		else{
			return '';
		}
	}
}
