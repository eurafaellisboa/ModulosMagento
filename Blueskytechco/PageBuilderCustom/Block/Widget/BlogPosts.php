<?php

declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class BlogPosts extends \Magefan\Blog\Block\Post\PostList\AbstractList implements BlockInterface, IdentityInterface
{
    static $processedIds = [];

    private $_templateFilterContent;

    protected $_categoryFactory;

    protected $_category;

    protected $_geDir;

    protected $_getFile;

    protected $_parser;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magefan\Blog\Model\Url $url,
        \Magefan\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Filesystem $file,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $postCollectionFactory, $url, $data);
        $this->_categoryFactory = $categoryFactory;
        $this->_templateFilterContent = $filterProvider;
        $this->_getFile = $file;
        $this->_parser = new \Magento\Framework\Xml\Parser();
        $this->_geDir = $this->_getFile->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Blueskytechco/PageBuilderCustom/size_thumbnail');
    }


    public function _toHtml()
    {
        if($this->getDataWidgetConfig('type_name') == 'carousel'){
            $this->setTemplate(
                $this->getDataWidgetConfig('template_carousel_id') ? 'Blueskytechco_PageBuilderCustom::widget/blog_posts/carousel/'.$this->getDataWidgetConfig('template_carousel_id').'.phtml' : 'Blueskytechco_PageBuilderCustom::widget/blog_posts/carousel/carousel.phtml'
            );
        }
        else{
            $this->setTemplate(
                $this->getDataWidgetConfig('template_id') ? 'Blueskytechco_PageBuilderCustom::widget/blog_posts/grid/'.$this->getDataWidgetConfig('template_id').'.phtml' : 'Blueskytechco_PageBuilderCustom::widget/blog_posts/grid/default.phtml'
            );
        }

        $html = parent::_toHtml();

        foreach ($this->getPostCollection() as $item) {
            self::$processedIds[$item->getId()] = $item->getId();
        }
        return $html;
    }

    public function getPostCollection()
    {
        if ($this->getData('store_id') == 0) {
            $collections = $this->_postCollectionFactory->create();
            $collections->addActiveFilter();
            $categoryIds = explode(',', $this->getDataWidgetConfig('category_id'));
            $collections->addCategoryFilter($categoryIds);
            if ($tagId = $this->getDataWidgetConfig('posts_tag')) {
                $collections->addTagFilter($tagId);
            }

            if ($authorId = $this->getDataWidgetConfig('posts_author')) {
                $collections->addAuthorFilter($authorId);
            }

            if ($from = $this->getDataWidgetConfig('publish_date_from')) {
                $collections->addFieldToFilter('publish_time', ['gteq' => $from . " 00:00:00"]);
            }

            if ($to = $this->getDataWidgetConfig('publish_date_to')) {
                $collections->addFieldToFilter('publish_time', ['lteq' => $to . " 00:00:00"]);
            }

            if ($this->getDataWidgetConfig('sort_order') == 'oldest') {
                $collections->setOrder('publish_time', 'ASC');
            }
            else{
                $collections->setOrder('publish_time', 'DESC');
            }
            $size = $this->getDataWidgetConfig('number_posts');
            if (!$size) {
                $size = (int) $this->_scopeConfig->getValue(
                    'mfblog/sidebar/recent_posts/posts_per_page',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            }

            $collections->setPageSize($size);
            return $collections;
        }
        
        if (is_null($this->_postCollection)) {
            $this->_preparePostCollection();
        }
        return $this->_postCollection;
    }

    protected function _preparePostCollection()
    {
        $size = $this->getDataWidgetConfig('number_posts');
        if (!$size) {
            $size = (int) $this->_scopeConfig->getValue(
                'mfblog/sidebar/recent_posts/posts_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        $this->setPageSize($size);

        parent::_preparePostCollection();

        $categoryIds = explode(',', $this->getDataWidgetConfig('category_id'));
        if (count($categoryIds) > 1) {
            $this->_postCollection->addCategoryFilter($categoryIds);
        } elseif ($category = $this->getCategory()) {
            $this->_postCollection->addCategoryFilter($category);
        }

        if ($tagId = $this->getDataWidgetConfig('posts_tag')) {
            $this->_postCollection->addTagFilter($tagId);
        }

        if ($authorId = $this->getDataWidgetConfig('posts_author')) {
            $this->_postCollection->addAuthorFilter($authorId);
        }

        if ($from = $this->getDataWidgetConfig('publish_date_from')) {
            $this->_postCollection
                ->addFieldToFilter('publish_time', ['gteq' => $from . " 00:00:00"]);
        }

        if ($to = $this->getDataWidgetConfig('publish_date_to')) {
            $this->_postCollection
                ->addFieldToFilter('publish_time', ['lteq' => $to . " 00:00:00"]);
        }

        if ($this->getDataWidgetConfig('sort_order') == 'oldest') {
            $this->_postCollection->setOrder('publish_time', 'ASC');
        }
        else{
            $this->_postCollection->setOrder('publish_time', 'DESC');
        }
        
    }

    public function getCategory()
    {
        if ($this->_category === null) {
            if ($categoryId = $this->getDataWidgetConfig('category_id')) {
                $category = $this->_categoryFactory->create();
                $category->load($categoryId);
                return $this->_category = $category;
            }

            $this->_category = false;
        }

        return $this->_category;
    }

    public function getDataWidgetConfig($path)
    {
        return $this->getData($path) ?: '';
    }

    public function filterOutputContent($content)
    {
        $content = (string) $content ?: '';
        if($content != ''){
            $arr_encode = ['^[','^]','`','|','&lt;','&gt;'];
            $arr_decode = ['{','}','"','\\','<','>'];
            $new_content = str_replace($arr_encode, $arr_decode, $content);
            return $this->_templateFilterContent->getPageFilter()->filter(
                (string) $new_content ?: ''
            );
        }
        return '';
    }

    public function getShorContent($post)
    {
        return $post->getShortFilteredContent();
    }

    public function getSizeThumbnail()
    {
        if (is_readable($this->_geDir.'/blog_posts.xml'))
        {
            $data_sizes = $this->_parser->load($this->_geDir.'/blog_posts.xml')->xmlToArray();
            $common = [];
            $type_view = $this->getDataWidgetConfig('type_name');
            $type_template = 'default';
            if(isset($data_sizes['root'][$type_view]['common'])){
                $common = $data_sizes['root'][$type_view]['common'];
            }
            $custom = [];
            if($this->getDataWidgetConfig('type_name') == 'carousel'){
                $type_template = $this->getDataWidgetConfig('template_carousel_id') ? $this->getDataWidgetConfig('template_carousel_id') : 'carousel';
            }
            else{
                $type_template = $this->getDataWidgetConfig('template_id') ? $this->getDataWidgetConfig('template_id') : 'default';
            }
            if(isset($data_sizes['root'][$type_view][$type_template])){
                $custom = $data_sizes['root'][$type_view][$type_template];
            }
            return ['common' => $common, 'custom' => $custom];
        }
        return [];
    }
}