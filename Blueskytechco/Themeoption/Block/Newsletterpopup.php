<?php 

namespace Blueskytechco\Themeoption\Block;

class Newsletterpopup extends  \Magento\Newsletter\Block\Subscribe
{
	protected $_filterProvider;
    protected $_blockFactory;
    
    public function __construct(
    	\Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }
	
	public function getConfig($value=''){
	   $config =  $this->_scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config;
	}

	public function getConfigNewlltes(){
		return $this->getConfig('themesetting/newsletter/enable');
	}

	public function getConfigNewlltesWidth(){
		return $this->getConfig('themesetting/newsletter/max_width');
	}

	public function getConfigProductPopupWidth(){
		return $this->getConfig('themesetting/product_popup/max_width') ? $this->getConfig('themesetting/product_popup/max_width') : '950px';
	}

	public function getConfigProductPopup(){
		return $this->getConfig('themesetting/product_popup/enable');
	}

	public function getConfigProductPopupTimeout(){
		return $this->getConfig('themesetting/product_popup/seconds_displayed');
	}

	public function getContentSetting(){
		$store_id = $this->_storeManager->getStore()->getId();
	   	$cmsblock =  $this->getConfig('themesetting/newsletter/cms_block');
	   	$block = $this->_blockFactory->create();
		$block->setStoreId($store_id)->load($cmsblock);
		$content = '';
		if($block){
			$content = ($block->getContent()) ? $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($block->getContent()) : '';
		}
	   return $content;
	}
	
	public function getproductsContentSetting(){
		$store_id = $this->_storeManager->getStore()->getId();
	   	$cmsblock =  $this->getConfig('themesetting/product_popup/cms_block');
	   	$block = $this->_blockFactory->create();
		$block->setStoreId($store_id)->load($cmsblock);
		$content = '';
		if($block){
			$content = ($block->getContent()) ? $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($block->getContent()) : '';
		}
	   return $content;
	}
}