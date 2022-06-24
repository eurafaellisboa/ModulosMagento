<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\Portfolio\Helper;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
/**
 * Contact base helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_storeManager;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_date;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_filter;
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_url;

	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Url $url
	) {
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
		$this->_date = $date;
		$this->_filter = $context->getFilterManager();
		$this->_filterProvider = $filterProvider;
		$this->_url = $url;
	}
	
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	/* Get system store config */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
	
	public function getModel(){
		return $this->_objectManager->create('MGS\Portfolio\Model\Category');
	}
	
	public function getPortfolios($cateid = NULL){
		$portfolios = $this->_objectManager->create('MGS\Portfolio\Model\Portfolio')
			->getCollection()
			->addStoreFilter($this->_storeManager->getStore()->getId())
			->addFieldToFilter('status', 1);
		
		if($cateid != null){
			$resourceModel = $this->_objectManager->create('MGS\Portfolio\Model\ResourceModel\Portfolio');
			$portfolios = $resourceModel->joinFilter($portfolios, $cateid);
		}	
		return $portfolios;
	}
	
	
	public function getCurrentPortfolios(){
		$portfolios = $this->getPortfolios();
		$id = $this->getRequest()->getParam('id');
		if($id != ""){
			$resourceModel = $this->_objectManager->create('MGS\Portfolio\Model\ResourceModel\Portfolio');
			$portfolios = $resourceModel->joinFilter($portfolios, $id);
		}
		
		return $portfolios;
	}
	
	public function getPortfolioAddress($portfolio){
		$identifier = $portfolio->getIdentifier();
		if($identifier!=''){
			return $this->getUrl('portfolio/'.$identifier);
		}
		return $this->getUrl('portfolio/index/view', ['id'=>$portfolio->getId()]);
	}
	public function getBaseImage(){
		$filePath = 'mgs/portfolio/image/';
		if($filePath!=''){
			$imageUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $filePath;
		}
		return $imageUrl;
	}
	public function getThumbnailSrc($portfolio){
		$filePath = 'mgs/portfolio/thumbnail/'.$portfolio->getThumbnailImage();
		if($filePath!=''){
			$thumbnailUrl = $this->_storeManager-> getStore()->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $filePath;
		}
		return $thumbnailUrl;
	}
	
	public function getCategories($portfolio){
		$collection = $this->_objectManager->create('MGS\Portfolio\Model\Stores')
			->getCollection()
			->addFieldToFilter('portfolio_id', $portfolio->getId());
		
		$resourceModel = $this->_objectManager->create('MGS\Portfolio\Model\ResourceModel\Stores');
		$collection = $resourceModel->joinFilter($collection);
		return $collection;
	}
	
	public function getCategoriesText($portfolio){
		$collection = $this->getCategories($portfolio);
		
		if($collection->getSize()>0){
			$arrResult = [];
			foreach($collection as $item){
				$arrResult[] = $item->getName();
			}
			return implode(', ', $arrResult);
		}
		return '';
	}
	
	public function getCategoriesLink($portfolio){
		$collection = $this->getCategories($portfolio);
		$html = '';
		if($collection->getSize()>0){
			$i=0;
			foreach($collection as $item){
				$cate = $this->_objectManager->create('MGS\Portfolio\Model\Category')
				->getCollection()
				->addFieldToFilter('category_id', ['eq' => $item->getCategoryId()])->getFirstItem();
				$i++;
				if($cate->getIdentifier()!=''){
					$html .= '<a href="'.$this->getUrl('portfolio/'.$cate->getIdentifier()).'">'.$item->getName().'</a>';
				}else{
					$html .= '<a href="'.$this->getUrl('portfolio/category/view', ['id'=>$cate->getId()]).'">'.$item->getName().'</a>';
				}
			}
		}
		return $html;
	}
	
	public function getMenu(){
		$menu = $this->getModel()->getCollection();

		foreach ($menu as $cate) {
			if($cate->getIdentifier()!=''){
				$cate->setLinkCate($this->getUrl('portfolio/'.$cate->getIdentifier()));
			}else{
				$cate->setLinkCate($this->getUrl('portfolio/category/view', ['id'=>$cate->getId()]));
			}
            
        }
		return $menu;
	}
	
	public function generateContentFilter($string){
		return $this->_filterProvider->getBlockFilter()->filter($string);
	}
	
	public function truncate($content, $length){
		return $this->filterManager->truncate($content, ['length' => $length, 'etc' => '']);
	}
	
	public function getFilterClass($portfolio){
		$i = 0;
		$html = "";
		$collection = $this->getCategories($portfolio);
		foreach($collection as $item){
			$i++;
			if($i > 1){
				$html .= ' ';
			}
			$html .= 'item_'.$item->getCategoryId();
		}
		return $html;
	}
	public function getRoute()
    {
        $route = $this->getConfig('general_settings/route');
        if ($this->getConfig('general_settings/route') == '') {
            $route = 'portfolio';
        }
        return $this->_storeManager->getStore()->getBaseUrl() . $route;
    }
}