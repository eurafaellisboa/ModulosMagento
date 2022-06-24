<?php

namespace MGS\Blog\Block\Post;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\RequestInterface;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;
    protected $_blogHelper;
    protected $_post;
    protected $_category;
    protected $httpContext;
    protected $request;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MGS\Blog\Helper\Data $blogHelper,
        \MGS\Blog\Model\Post $post,
        \MGS\Blog\Model\Category $category,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    )
    {
        $this->_post = $post;
        $this->_category = $category;
        $this->_coreRegistry = $registry;
        $this->request = $context->getRequest();
        $this->_blogHelper = $blogHelper;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if (!$this->getConfig('general_settings/enabled')) return;
        parent::_construct();
    }

    public function getCacheKeyInfo()
    {
        return [
            'BLOG_POST_VIEW',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate()
        ];
    }

    protected function _addBreadcrumbs()
    {
        $category = $this->getParentCategory();
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $pageTitle = $this->_blogHelper->getConfig('general_settings/title');
        $post = $this->getCurrentPost();
		if($breadcrumbsBlock){
			$breadcrumbsBlock->addCrumb(
				'home',
				[
					'label' => __('Home'),
					'title' => __('Go to Home Page'),
					'link' => $baseUrl
				]
			);
			$breadcrumbsBlock->addCrumb(
				'blog',
				[
					'label' => $pageTitle,
					'title' => $pageTitle,
					'link' => $this->_blogHelper->getRoute()
				]
			);
			if ($category != false) {
				$breadcrumbsBlock->addCrumb(
					'category',
					[
						'label' => $category->getTitle(),
						'title' => $category->getTitle(),
						'link' => $category->getCategoryUrl()
					]
				);
			}
			$breadcrumbsBlock->addCrumb(
				'post',
				[
					'label' => $post->getTitle(),
					'title' => $post->getTitle(),
					'link' => ''
				]
			);
		}
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_blogHelper->getConfig($key);
        if (!$result) {
            return $default;
        }
        return $result;
    }

    public function getCurrentPost()
    {
        $post = $this->_coreRegistry->registry('current_post');
        if ($post) {
            $this->setData('current_post', $post);
        }
        return $post;
    }

    protected function _prepareLayout()
    {
        $post = $this->getCurrentPost();
        $pageTitle = $post->getTitle();
        $metaKeywords = $post->getMetaKeywords();
        $metaDescription = $post->getMetaDescription();
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('blog-post-view');
        if ($pageTitle) {
            $this->pageConfig->getTitle()->set($pageTitle);
        }
        if ($metaKeywords) {
            $this->pageConfig->setKeywords($metaKeywords);
        }
        if ($metaDescription) {
            $this->pageConfig->setDescription($metaDescription);
        }
        return parent::_prepareLayout();
    }

    public function getParentCategory()
    {
        $urlKey = trim($this->request->getPathInfo(), '/');
        $identifiers = explode('/', $urlKey);
        if (count($identifiers) == 3) {
            $identifier = $identifiers[1];
            $category = $this->_category->getCollection()
                ->addFieldToFilter('status', array('eq' => 1))
                ->addFieldToFilter('url_key', array('eq' => $identifier))
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->getFirstItem();
            if ($category && $category->getId() && (in_array($this->_storeManager->getStore()->getId(), $category->getStoreId()) || in_array(0, $category->getStoreId()))) {
                return $category;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
	
	public function getParentCategories($postId)
    {
		$catetories = $this->_category->getCollection()
            ->addPostFilter($postId)
            ->addStoreFilter($this->_storeManager->getStore()->getId());
			
        return $catetories;
    }
	
	public function getRelatedPosts($postId)
    {
		$catetories = $this->getParentCategories($postId);
		$categoryIds = [];
		if(count($catetories)){
			foreach ($catetories as $_category){
				$categoryIds[] = $_category->getCategoryId();
			}
			$categoryIds = implode(',', $categoryIds);
		}
		if($categoryIds){
			$postCollection = $this->_post->getCollection()
				->addFieldToFilter('status', 1)
				->addCategoryFilter($categoryIds)
				->addStoreFilter($this->_storeManager->getStore()->getId())
				->setPageSize(6)
				->setCurPage(1)
				->setOrder('created_at', $this->getConfig('general_settings/default_sort'));
				
			return $postCollection;
		}
		
		return;
    }

    public function getPostUrl()
    {
        $category = $this->getParentCategory();
        if ($category != false) {
            return $this->getCurrentPost()->getPostUrlWithCategory($category->getId());
        } else {
            return $this->getCurrentPost()->getPostUrlWithNoCategory();
        }
    }
	
	
	public function getAllPost() {
		$post = $this->_post;
        $postCollection = $post->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('created_at', $this->getConfig('general_settings/default_sort'));
			
		return $postCollection;
	}
	
	
	public function getAllPostId(){
        $postCollection = $this->getAllPost();
		$arrResult = [];
		if(count($postCollection)>0){
			foreach($postCollection as $item){
				$arrResult[] = ['id'=>$item->getId(), 'value'=>$item->getUrlKey(), 'name'=>$item->getTitle()];
			}
		}
		return $arrResult;
	}
	
	public function getUrlPostById($id,$value,$name){
		$arrResult = [];
		$route = $this->_blogHelper->getRoute();
		$route = $route . '/' . $value;
		$arrResult = ['id'=>$id,'url'=>$route, 'name'=>$name];
		return $arrResult;
	}
	
	public function getNextPost($id){
		$arrId = $this->getAllPostId();
		if(is_array($arrId)){
			$maxKey = count($arrId) - 1;
			$key = array_search($id, array_column($arrId, 'id'));
			if($key == $maxKey){
				$nextKey = 0;
			}else {
				$nextKey = $key + 1;
			}
			$idNext = $arrId[$nextKey]['id'];
			$valueNext = $arrId[$nextKey]['value'];
			$nameNext = $arrId[$nextKey]['name'];
			
			return $this->getUrlPostById($idNext,$valueNext,$nameNext);
		}
		return;
	}
	
	public function getPrevPost($id){
		$arrId = $this->getAllPostId();
		if(is_array($arrId)){
			$maxKey = count($arrId) - 1;
			$key = array_search($id, array_column($arrId, 'id'));
			if($key == 0){
				$nextKey = $maxKey;
			}else {
				$nextKey = $key - 1;
			}
			$idNext = $arrId[$nextKey]['id'];
			$valueNext = $arrId[$nextKey]['value'];
			$nameNext = $arrId[$nextKey]['name'];
			
			return $this->getUrlPostById($idNext,$valueNext,$nameNext);
		}
		return;
	}
    
    public function getGalleryImage($post){
		if($post->getGalleryImage()){
			$result = [];
			$gallery = $post->getGalleryImage();
			$galleryArray = explode(',',$gallery);
			if(count($galleryArray)>0){
				foreach($galleryArray as $img){
					$filePath = 'mgs_blog/gallery/image'.$img;
					if($filePath!=''){
						$imageUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $filePath;
						$result[] = $imageUrl;
					}
				}
			}
			return $result;
		}
		return 0;
	}
	
	public function getCategories()
    {
        $category = $this->_category;
        $categoryCollection = $category->getCollection()
            ->addFieldToFilter('status', 1);
        $categoryCollection->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('sort_order', 'ASC');
        return $categoryCollection;
    }
	
	public function getTags()
    {
        $postCollection = $this->_post->getCollection()
            ->addFieldToFilter('status', 1);
        $postCollection->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('created_at', 'DESC');
        $tags = [];
        foreach ($postCollection as $post) {
            $postTags = explode(',', $post->getTags());
            foreach ($postTags as $tag) {
                if ($tag == null || $tag == '') continue;
                $tags[] = trim($tag);
            }
        }
        return array_count_values($tags);
    }
	 public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
