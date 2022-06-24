<?php

namespace Blueskytechco\CmsPageBanner\Block\PageBanner;

class BannerImage extends \Magento\Framework\View\Element\Template
{
    protected $_page;
    protected $_storeManager;
    protected $_pageFactory;
    protected $_imageUploader;
    protected $_logo;

    public function __construct(
        \Magento\Cms\Model\Page $page,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Blueskytechco\CmsPageBanner\Model\ImageUploader $imageUploader,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        array $data = []
    ) {
        $this->_page = $page;
        $this->_storeManager = $storeManager;
        $this->_pageFactory = $pageFactory;
        $this->_imageUploader = $imageUploader;
        $this->_logo = $logo;
        parent::__construct($context, $data);
    }

     public function isHomePage()
    {
        return $this->_logo->isHomePage();
    }

    public function getPageId()
    {
        $pageId = $this->_page->getId();
        return $pageId;
    }

    public function getPage()
    {
        if ($this->getPageId()) {
            $page = $this->_pageFactory->create();
            $page->setStoreId($this->_storeManager->getStore()->getId())->load($this->getPageId());
        } else {
            $page = $this->_page;
        }
        return $page;
    }

    public function getConfig($config_path)
    {
        return $this->_scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getBannerImages()
    {
        $page = $this->getPage();
        $bg = '';
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $default_banner = $this->getConfig('themesetting/general/default_banner');
        if($default_banner && $default_banner != ''){
            $url_img_banner = $mediaUrl.'blueskytechco/banner/'.$default_banner;
            $bg = " style=\"background-image: url('".$url_img_banner."');\"";
        }

        if($page->getImageField() != ''){
            $url_img_banner = $mediaUrl.$this->_imageUploader->getBasePath().'/'.$page->getImageField();
            $bg = " style=\"background-image: url('".$url_img_banner."');\"";
        }
        return $bg;
    }
}
