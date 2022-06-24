<?php

namespace MGS\Amp\Block\Page;

use Magento\Framework\View\Element\Template;

class AmpHome extends Template {
	
	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var MGS\Amp\Helper\Config
     */
    protected $_configHelper;
	
    /**
     * @var Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Zemez\Amp\Helper\Data $helper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MGS\Amp\Helper\Config $configHelper,
		\Magento\Cms\Model\PageFactory $pageFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_configHelper = $configHelper;
		$this->_filterProvider = $filterProvider;
		$this->_pageFactory = $pageFactory;
    }
	
	public function getContentAmp(){
		$content = $this->getCmsPageContent();
		return $this->_filterProvider->getPageFilter()->filter($this->replaceTeamplate($content));
	}
	
	protected function getCmsForAmp(){
		return $this->_configHelper->getStoreConfig('mgs_amp/general/cms_home_mobile');
	}
	
	protected function getCmsPageContent(){
		$csmPageContent = '';
		$cmsPage = $this->getCmsForAmp();
		if ($cmsPage) {
			$csmPageContent = $this->_pageFactory->create()->load($cmsPage, 'identifier')->getContent();
		}
		
		return $csmPageContent;
	}
	
	protected function replaceTeamplate($content){
		/* Slider Block */
		$content = str_replace('template="widget/owl_slider.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/owl_slider.phtml"',$content);
		/* Promobanner Block */
		$content = str_replace('template="widget/promobanner.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/promobanner.phtml"',$content);
		/* Product Block */
		$content = str_replace('template="products/grid.phtml"','template="MGS_Amp::MGS_Fbuilder/products/grid.phtml"',$content);
		
		/* Blog Widget Block */
		$content = str_replace('template="MGS_Fbuilder::widget/blog/grid.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/blog/grid.phtml"',$content);
		
		/* Instagram Block */
		$content = str_replace('template="widget/socials/instagram.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/socials/instagram.phtml"',$content);
		
		/*  Products Block Category Tabs */
		$content = str_replace('template="products/category-tabs.phtml"','template="MGS_Amp::MGS_Fbuilder/products/category-tabs.phtml"',$content);
		
		/*  Products Block list */
		$content = str_replace('template="products/list.phtml"','template="MGS_Amp::MGS_Fbuilder/products/grid.phtml"',$content);
		
		/*  Products Block Grid */
		$content = str_replace('template="products/grid.phtml"','template="MGS_Amp::MGS_Fbuilder/products/grid.phtml"',$content);
		
		/*  Deals */
		$content = str_replace('template="products/deals/category-tabs.phtml"','template="MGS_Amp::MGS_Fbuilder/products/deals/category-tabs.phtml"',$content);
		$content = str_replace('template="products/deals/list.phtml"','template="MGS_Amp::MGS_Fbuilder/products/deals/grid.phtml"',$content);
		$content = str_replace('template="products/deals/grid.phtml"','template="MGS_Amp::MGS_Fbuilder/products/deals/grid.phtml"',$content);
		
		/* Product Tabs Block */
		$content = str_replace('template="products/tabs/view.phtml"','template="MGS_Amp::MGS_Fbuilder/products/tabs/view.phtml"',$content);
		
		/* Single Product Block */
		$content = str_replace('template="products/single/default.phtml"','template="MGS_Amp::MGS_Fbuilder/products/single/default.phtml"',$content);
		$content = str_replace('template="products/single/deals.phtml"','template="MGS_Amp::MGS_Fbuilder/products/single/deals.phtml"',$content);
		
		
		/* Image Block */
		$content = str_replace('template="MGS_Fbuilder::widget/image/multiple.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/image/multiple.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/image/single.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/image/single.phtml"',$content);
		
		/* Profile Block */
		$content = str_replace('template="widget/profile/circle.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/profile/horizontal.phtml"',$content);
		$content = str_replace('template="widget/profile/horizontal.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/profile/horizontal.phtml"',$content);
		$content = str_replace('template="widget/profile/vertical.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/profile/horizontal.phtml"',$content);

		/* Accordion Block */
		$content = str_replace('template="MGS_Fbuilder::widget/accordion.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/accordion.phtml"',$content);
		
		/* Video Block */
		$content = str_replace('template="widget/video.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/video.phtml"',$content);
		
		/* Tetimonials Block */
		$content = str_replace('template="MGS_Fbuilder::widget/tetimonials.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/tetimonials.phtml"',$content);
		
		/* Portfolio Block */
		$content = str_replace('template="MGS_Fbuilder::widget/portfolio.phtml"','template="MGS_Amp::MGS_Fbuilder/widget/portfolio.phtml"',$content);
		
		/* Remove other block */
		$content = str_replace('template="widget/socials/facebook_fanbox.phtml""','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="widget/socials/twitter_timeline.phtml"','template="MGS_Amp::blank.phtml"',$content);
		
		$content = str_replace('template="widget/video.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="widget/socials/snapppt.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="widget/map.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/content_box.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/counter_box.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/countdown_box.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/progress_bar.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="widget/progress_circle.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/chart.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/list.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Lookbook::widget/lookbook.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Lookbook::widget/slider.phtml"','template="MGS_Amp::blank.phtml"',$content);
		
		$content = str_replace('template="MGS_Fbuilder::widget/masonry.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/static_tabs.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/modal_popup.phtml"','template="MGS_Amp::blank.phtml"',$content);
		$content = str_replace('template="MGS_Fbuilder::widget/form.phtml"','template="MGS_Amp::blank.phtml"',$content);
		
		return $content;
	}
}