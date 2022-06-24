<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\ThemeSettings\Block\Page;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Main contact form block
 */
class Head extends \MGS\Fbuilder\Block\Page\Head
{	
	protected $_generateHelper;
	protected $_storeManager;
	protected $session;
	protected $themeSetting;
	protected $request;
	public function __construct(
        Context $context,
		\MGS\Fbuilder\Helper\Generate $_generateHelper,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\MGS\ThemeSettings\Helper\Config $themeSettingConfig,
		\Magento\Framework\App\RequestInterface $request
    )
    {
		$this->session = $session;
		$this->themeSetting = $themeSettingConfig;
        parent::__construct($context, $_generateHelper);
		$this->request = $request;

		if(
			$this->request->getModuleName()!='mgsthemesetting' && 
			$this->request->getModuleName()!='fbuilder' &&
			$this->request->getModuleName()!='ajaxcart' &&
			$this->request->getModuleName()!='aquickview' &&
			$this->request->getModuleName()!='guestwishlist' &&
			$this->request->getModuleName()!='instantsearch'
		){
			$this->session->setFrameFullActionName($this->_request->getFullActionName());
		}
		
    }
	
	protected function _prepareLayout(){
		if($this->getStoreConfig('themesettings/general/dark_theme')){
			$this->pageConfig->addPageAsset('css/mgs_theme_dark.css');
			$this->pageConfig->addBodyClass('dark_theme');
		}
		if($this->getStoreConfig('themesettings/general/rtl_theme')){
			$this->pageConfig->addPageAsset('css/mgs_theme_rtl.css');
			$this->pageConfig->addBodyClass('rtl_theme');
		}
		return parent::_prepareLayout();
	}
	
	public function isCustomize(){
		if($this->session->getThemeCustomize()){
			return true;
		}
		return false;
	}
	
	public function getStyleInline(){
		if($this->session->getStyleInline()){
			return $this->session->getStyleInline();
		}else{
			return $this->themeSetting->getStyleInline($this->_storeManager->getStore()->getId());
		}
	}
}