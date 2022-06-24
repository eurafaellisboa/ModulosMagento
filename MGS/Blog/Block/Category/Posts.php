<?php

namespace MGS\Blog\Block\Category;

use Magento\Customer\Model\Context as CustomerContext;

class Posts extends \Magento\Framework\View\Element\Template
{
    protected $_coreRegistry = null;
    protected $_blogHelper;
    protected $_post;
    protected $_category;
    protected $httpContext;
    protected $_comment;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \MGS\Blog\Helper\Data $blogHelper,
        \MGS\Blog\Model\Post $post,
        \MGS\Blog\Model\Category $category,
        \Magento\Framework\App\Http\Context $httpContext,
        \MGS\Blog\Model\Comment $comment,
        array $data = []
    )
    {
        $this->_post = $post;
        $this->_category = $category;
        $this->_coreRegistry = $registry;
        $this->_blogHelper = $blogHelper;
        $this->httpContext = $httpContext;
        $this->_comment = $comment;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if (!$this->getConfig('general_settings/enabled')) return;
        parent::_construct();
        $category = $this->getCurrentCategory();
        $post = $this->_post;
        $postCollection = $post->getCollection()
            ->addFieldToFilter('status', 1)
            ->addCategoryFilter($category->getId())
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('created_at', $this->getConfig('general_settings/default_sort'));
        $this->setCollection($postCollection);
    }

    public function getCacheKeyInfo()
    {
        return [
            'BLOG_POST_LIST',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate()
        ];
    }

    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $pageTitle = $this->_blogHelper->getConfig('general_settings/title');
        $category = $this->getCurrentCategory();
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
        $breadcrumbsBlock->addCrumb(
            'category',
            [
                'label' => $category->getTitle(),
                'title' => $category->getTitle(),
                'link' => ''
            ]
        );
    }

    public function getParentCategory($postId){
        $postCollection = $this->_category->getCollection()
                                ->addFieldToFilter('status', 1)
                                ->addPostFilter($postId);
        return $postCollection;
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_blogHelper->getConfig($key);
        if (!$result) {
            return $default;
        }
        return $result;
    }

    public function getCurrentCategory()
    {
        $category = $this->_coreRegistry->registry('current_category');
        if ($category) {
            $this->setData('current_category', $category);
        }
        return $category;
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
    protected function _prepareLayout()
    {
		if($this->getConfig('general_settings/blog_show') == 'blog_grid'){
			$this->setTemplate('MGS_Blog::category/posts-grid.phtml');
		}else if($this->getConfig('general_settings/blog_show') == 'blog_list') {
			$this->setTemplate('MGS_Blog::category/posts-list.phtml');
		}else {
            $this->setTemplate('MGS_Blog::category/posts-masorry.phtml');
        }
        $category = $this->getCurrentCategory();
        $pageTitle = $category->getTitle();
        $metaKeywords = $category->getMetaKeywords();
        $metaDescription = $category->getMetaDescription();
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('blog-post-list');
        if ($pageTitle) {
            $this->pageConfig->getTitle()->set($pageTitle);
        }
        if ($metaKeywords) {
            $this->pageConfig->setKeywords($metaKeywords);
        }
        if ($metaDescription) {
            $this->pageConfig->setDescription($metaDescription);
        }
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'blog.post.list.pager'
            );
            $pager->setLimit($this->getConfig('general_settings/posts_per_page'))->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
        }
        return parent::_prepareLayout();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getCountCommentInPost($postId = null)
    {
        $comment = $this->_comment->getCollection()->addFieldToFilter('post_id', $postId);
        return $count = $comment->count();
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
}
