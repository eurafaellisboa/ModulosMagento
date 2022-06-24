<?php

namespace Blueskytechco\Themeoption\Block\PageBanner;

class BannerImage extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
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
        $bg = '';
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $default_banner = $this->getConfig('themesetting/general/default_banner');
        if($default_banner && $default_banner != ''){
            $url_img_banner = $mediaUrl.'blueskytechco/banner/'.$default_banner;
            $bg = " style=\"background-image: url('".$url_img_banner."');\"";
        }

        return $bg;
    }
}