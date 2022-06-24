<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Portfolio\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Category extends Template
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;
	
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
		Template\Context $context, array $data = [], 
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\ObjectManagerInterface $objectManager
	)
    {
        parent::__construct($context, $data);
		$this->_objectManager = $objectManager;
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
    }
	
	/**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
		if($this->getStoreConfig('portfolio/general_settings/portfolio_show') == 'portfolio_carousel'){
			$this->setTemplate('MGS_Portfolio::layouts/list-carousel.phtml');
			$this->pageConfig->addBodyClass('portfolio-list-grid');
		}elseif($this->getStoreConfig('portfolio/general_settings/portfolio_show') == 'portfolio_masonry'){
			$this->setTemplate('MGS_Portfolio::layouts/list-masonry.phtml');
			$this->pageConfig->addBodyClass('portfolio-list-masonry');
		}else {
			$this->setTemplate('MGS_Portfolio::layouts/list-grid.phtml');
			$this->pageConfig->addBodyClass('portfolio-list-grid');
		}
		
		$title = __('Portfolio');
		if($id = $this->getRequest()->getParam('id')){
			$category = $this->_objectManager->create('MGS\Portfolio\Model\Category')->load($id);
			$title = $category->getCategoryName();
		}
		
		$breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
		if($breadcrumbsBlock){
			$breadcrumbsBlock->addCrumb(
				'home',
				[
					'label' => __('Home'),
					'title' => __('Go to Home Page'),
					'link' => $this->_storeManager->getStore()->getBaseUrl()
				]
			);
			$breadcrumbsBlock->addCrumb('portfolio_category', ['label' => $title, 'title' => $title]);
		}	
		$this->pageConfig->getTitle()->set($title);	
			
        return parent::_prepareLayout();
    }
	
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
	}
	
	public function getAllPortfolios(){
		$portfolios = $this->_objectManager->create('MGS\Portfolio\Model\Portfolio')
			->getCollection()
			->addStoreFilter($this->_storeManager->getStore()->getId())
			->addFieldToFilter('status', 1);
		
		return $portfolios;
	}
	
	public function getPortfolios(){
		$portfolios = $this->getAllPortfolios();
		
		$id = $this->getRequest()->getParam('id');
		if($id != ""){
			$portfolios = $portfolios->addCategoryFilter($id);
		}
		
		return $portfolios;
	}
	
	protected function getStoreCategories(){
		$cateIds = [];
		$categories = $this->_objectManager->create('MGS\Portfolio\Model\Category')
			->getCollection()
			->addStoreFilter($this->_storeManager->getStore()->getId());
		
		if($categories->getSize()>0){
			foreach($categories as $_category){
				$cateIds[] = $_category->getCategoryId();
			}
		}
		return $cateIds;
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
	
	public function getThumbnailSrc($portfolio){
		$filePath = 'mgs/portfolio/thumbnail/'.$portfolio->getThumbnailImage();
		if($filePath!=''){
			$thumbnailUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $filePath;
			return $thumbnailUrl;
		}
		return 0;
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
				
				->addFieldToFilter('category_id', ['eq' 
				=> $item->getCategoryId()])->getFirstItem();
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
		$menu = $this->_objectManager->create('MGS\Portfolio\Model\Category')->getCollection()->addStoreFilter($this->_storeManager->getStore()->getId());

		if(count($menu)){
			foreach ($menu as $cate) {
				if($cate->getIdentifier()!=''){
					$cate->setLinkCate($this->getUrl('portfolio/'.$cate->getIdentifier()));
				}else{
					$cate->setLinkCate($this->getUrl('portfolio/category/view', ['id'=>$cate->getId()]));
				}
			}
		}
		
		return $menu;
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
}

