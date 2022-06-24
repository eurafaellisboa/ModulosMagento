<?php
namespace Blueskytechco\CustomCatalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Blueskytechco\CustomCatalog\Helper\Data as Helper;

class LayoutLoadBefore implements ObserverInterface
{

    protected $_registry;
	protected $_scopeConfig;
	protected $_pageResult;
	protected $request;
    protected $helper;

    public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Page\Config $pageConfig,
        Helper $helper,
        Registry $registry
    ){
        $this->helper = $helper;
        $this->_registry = $registry;
		$this->request = $request;
		$this->_pageConfig = $pageConfig;
		$this->_scopeConfig = $scopeInterface;
    }

    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
		$params = $this->request->getParams();
        if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'categorytab/category/view') !== false || strpos($_SERVER['REQUEST_URI'], 'blueskytechco_quickview/product/view') !== false){
            return $this;
        }
        $layout = $observer->getData('layout');
		
		/* Category page */
        $category = $this->_registry->registry('current_category');
        if($action == 'catalog_category_view' && $category){
            $page_layout_view = '';
            $page_load_more = '';

            if($this->helper->getData('themesetting/category/page_layout') != ''){
                $page_layout_view = $this->helper->getData('themesetting/category/page_layout');
            }

            if(isset($params['view']) && $params['view']){
                $page_layout_view = $params['view'];
            }

            if($this->helper->getData('themesetting/category/load_more_ajax') != ''){
                $page_load_more = $this->helper->getData('themesetting/category/load_more_ajax');
            }
            
            if(isset($params['load-more']) && $params['load-more']){
                $page_load_more = $params['load-more'];
            }
            
			$this->updateLayoutPageCategory(
                $layout,
                $page_layout_view,
                $page_load_more
            );
			return $this;
        }
		
		/* Product page */
		$product = $this->_registry->registry('product');
		if($action == 'catalog_product_view' && $product){
            $page_layout_view = '';
            $page_layout_sticky_sidebar = '';
            $page_layout_tab_accordions = '';

            if($this->helper->getData('themesetting/product/product_page_layout') != ''){
                $product_layout = $this->helper->getData('themesetting/product/product_page_layout');
                if($product_layout == 'product_style_1'){
                    $page_layout_view = 'layout-1';
                }
                elseif($product_layout == 'product_style_2'){
                    $page_layout_view = 'layout-2';
                }
                elseif($product_layout == 'product_style_3'){
                    $page_layout_view = 'layout-3';
                }
            }

            if(isset($params['view']) && $params['view']){
                $page_layout_view = $params['view'];
            }

            if($this->helper->getData('themesetting/product/enable_product_sticky') == 'enable'){
                $page_layout_sticky_sidebar = 'enable';
            }
            
            if(isset($params['sticky-sidebar']) && $params['sticky-sidebar'] == 'enable'){
                $page_layout_sticky_sidebar = $params['sticky-sidebar'];
            }

            if($this->helper->getData('themesetting/product/display_custom_tabs') != ''){
                $page_layout_tab_accordions = $this->helper->getData('themesetting/product/display_custom_tabs');
            }

            if(isset($params['tab']) && $params['tab']){
                $page_layout_tab_accordions = $params['tab'];
            }

			$this->updateLayoutPageProduct(
                $layout,
                $page_layout_view,
                $page_layout_sticky_sidebar,
                $page_layout_tab_accordions
            );
			return $this;
		}
		
		/* Blog page */
        $blog_category = $this->_registry->registry('current_blog_category');
        if ($action == 'blog_index_index' || ($action == 'blog_category_view' && $blog_category)) { 
            $blog_layout = '';
            if($this->helper->getData('themesetting/blog/page_layout') != ''){
                $blog_layout = $this->helper->getData('themesetting/blog/page_layout');
            }
            if(isset($params['blog-layout'])){
                $blog_layout = $params['blog-layout'];
            }
            $layout = $observer->getData('layout');
            $this->updateLayoutPageBlog(
                $layout,
                $blog_layout
            );
			return $this;
        }
        return $this;
    }

    public function updateLayoutPageCategory(
        $layout,
        $page_layout_view = null,
        $page_load_more = null
    ){
        if ($page_layout_view) {
            switch ($page_layout_view) {
                case 'grid':
                    $layout->getUpdate()->addHandle('catalog_category_view_'.$page_layout_view.'');
                    break;
                case 'packery':
                    $layout->getUpdate()->addHandle('catalog_category_view_'.$page_layout_view.'');
                    break;
                case 'masonry':
                    $layout->getUpdate()->addHandle('catalog_category_view_'.$page_layout_view.'');
                    break;
                case 'fullwidth':
                    $layout->getUpdate()->addHandle('catalog_category_view_'.$page_layout_view.'');
                    break;
                case 'sidebar-canvas':
                    $layout->getUpdate()->addHandle('catalog_category_view_'.$page_layout_view.'');
                    break;
            }
            $this->_pageConfig->addBodyClass('catalog-category-'.$page_layout_view.'');
        }
        if ($page_load_more && ( $page_load_more == 'button' || $page_load_more == 'scroll' )) {
            $layout->getUpdate()->addHandle('catalog_category_view_load_more');
        }
    }

    public function updateLayoutPageProduct(
        $layout,
        $view = null,
        $sticky_sidebar = null,
        $tab_accordions = null
    ){
        $add_class_layout = '';
        if ($view) {
            if ($tab_accordions && $tab_accordions == 'accordions-2') {
                $layout->getUpdate()->addHandle('catalog_product_view_layout1');
                $add_class_layout .= 'product-layout-1';
            } else {
                switch ($view) {
                    case 'layout-1':
                        $layout->getUpdate()->addHandle('catalog_product_view_layout1');
                        $add_class_layout .= 'product-layout-1';
                        break;
                    case 'layout-2':
                        $layout->getUpdate()->addHandle('catalog_product_view_layout_grid');
                        $add_class_layout .= 'product-layout-2';
                        break;
                    case 'layout-3':
                        $layout->getUpdate()->addHandle('catalog_product_view_layout_grid');
                        $add_class_layout .= 'product-layout-3';
                        break;
                }
            }
            $this->_pageConfig->addBodyClass($add_class_layout);
        }
        if ($tab_accordions) {
            switch ($tab_accordions) {
                case 'accordions':
                    $layout->getUpdate()->addHandle('catalog_product_view_tab_accordions');
                    break;
                case 'accordions-2':
                    $layout->getUpdate()->addHandle('catalog_product_view_tab_accordions2');
                    break;
            }
        }
        if ($sticky_sidebar) {
            $this->_pageConfig->addBodyClass('sticky-sidebar-enable');
        }
    }

    public function updateLayoutPageBlog(
        $layout,
        $blog_layout = null
    ){
        $add_class_layout = '';
        switch ($blog_layout) {
            case 'grid':
                $layout->getUpdate()->addHandle('blog_layout_grid');
                $add_class_layout .= 'blog-layout-grid';
                break;
            case 'masonry':
                $layout->getUpdate()->addHandle('blog_layout_masonry');
                $add_class_layout .= 'blog-layout-masonry';
                break;
            case 'sidebar-left':
                $layout->getUpdate()->addHandle('blog_layout_sidebar_left');
                $add_class_layout .= 'blog-layout-sidebar-left';
                break;
            default:
                $layout->getUpdate()->addHandle('blog_layout_sidebar_right');
                $add_class_layout .= 'blog-layout-sidebar-right';
                break;
        }
        $this->_pageConfig->addBodyClass($add_class_layout);
    }

}
?>