<?php

namespace MGS\Amp\Block\Page\Head\Json;

use Magento\Store\Model\ScopeInterface;

class Category extends \Magento\Framework\View\Element\Template
{
    const NULL_CATEGORY_NAME = 'Category Name';
    const NULL_CATEGORY_DESCRIPTION = 'Category Description';
    const LOGO_IMAGE_WIDTH = 270;
    const LOGO_IMAGE_HEIGHT = 60;
    const THUMB_WIDTH = 100;
    const THUMB_HEIGHT = 100;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Zemez\Amp\Helper\Data $helper,
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Retrieve string by JSON format according to http://schema.org requirements
     * @return string
     */
    public function getJson()
    {
        $siteName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
        if (!$siteName) {
            $siteName = 'Magento Store';
        }

        $logoBlock = $this->getLayout()->getBlock(' logo');
        $logo = $logoBlock ? $logoBlock->getLogoSrc() : '';
        $currentCategory = $this->_coreRegistry->registry('current_category');
        $categoryName = $currentCategory->getName() ? $currentCategory->getName() : self::NULL_CATEGORY_NAME;
        $categoryDescription = $this->pageConfig->getDescription() ? mb_substr($this->pageConfig->getDescription(), 0, 250, 'UTF-8') : self::NULL_CATEGORY_DESCRIPTION;
        $categoryCreatedAt = $currentCategory->getCreatedAt() ? $currentCategory->getCreatedAt() : '';
        $categoryUpdatedAt = $currentCategory->getUpdatedAt() ? $currentCategory->getUpdatedAt() : '';

        if ($this->pageConfig->getTitle()->get()) {
            $pageContentHeading = $this->pageConfig->getTitle()->get();
        } else {
            $pageContentHeading = $categoryName;
        }

        $categoryThumb = (string)$currentCategory->getImageUrl();
        if($categoryThumb) {
            $dataImageObject = array(
                '@type' => 'ImageObject',
                'url' => $categoryThumb,
                'width' => self::THUMB_WIDTH,
                'height' => self::THUMB_HEIGHT,
            );
        } else {
            $dataImageObject = array(
                '@type' => 'ImageObject',
                'url' => $logo,
                'width' => 696,
                'height' => self::LOGO_IMAGE_HEIGHT,
            );
        }

        // Set scheme JSON data

        $json = array(
            "@context" => "http://schema.org",
            "@type" => "Article",
            "author" => $siteName,
            "image" => $dataImageObject,
            "name" => $categoryName,
            "description" => $categoryDescription,
            "datePublished" => $categoryCreatedAt,
            "dateModified" => $categoryUpdatedAt,
            "headline" => mb_substr($pageContentHeading, 0, 110, 'UTF-8'),
            "publisher" => array(
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $logo,
                ),
            ),
            "mainEntityOfPage" => array(
                "@type" => "WebPage",
                "@id" => $this->getUrl(),
            ),
        );

        return str_replace('\/', '/', json_encode($json));
    }
}