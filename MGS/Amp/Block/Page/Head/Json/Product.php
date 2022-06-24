<?php

namespace MGS\Amp\Block\Page\Head\Json;

use Magento\Store\Model\ScopeInterface;

class Product extends \Magento\Catalog\Block\Product\AbstractProduct
{
    const NULL_PRODUCT_NAME = 'Null_Product_Name';
    const NULL_PRODUCT_SHORT_DESCRIPTION = 'Null_Product_short_description';
    const NULL_PRODUCT_STATUS = 'OutOfStock';
    const DEFAULT_CURRENCY = 'USD';
    const PRODUCT_IMAGE_WIDTH = 480;
    const PRODUCT_IMAGE_HEIGHT = 480;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Zemez\Amp\Helper\Data $helper,
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve string by JSON format according to http://schema.org requirements
     * @return string
     */
    public function getJson()
    {
        /**
         * Get helper, product and store objects
         */
        $_product = $this->getProduct();
        $_store = $_product->getStore();

        /**
         * Set product default values
         */
        $productName = self::NULL_PRODUCT_NAME;
        $productShortDescription = self::NULL_PRODUCT_SHORT_DESCRIPTION;
        $productStatus = self::NULL_PRODUCT_STATUS;
        $productPrice = 0;
        $productPriceCurrency = self::DEFAULT_CURRENCY;

        /**
         * Set product data from product object
         */
        if ($_product) {
            /**
             * Get product name
             */
            if (strlen($_product->getName())) {
                $productName = $this->escapeHtml($_product->getName());
            }

            /**
             * Get product image
             */
            $productImage = $this->getImage($_product, 'product_page_image_small', [])->getData('image_url');

            /**
             * Get product description
             */
            if (strlen($_product->getShortDescription())) {
                $productShortDescription = $this->escapeHtml($_product->getShortDescription());
            }
        }

        $siteName = $this->_scopeConfig->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE);
        if (!$siteName) {
            $siteName = 'Magento Store';
        }

        $logoBlock = $this->getLayout()->getBlock(' logo');
        $logo = $logoBlock ? $logoBlock->getLogoSrc() : '';

        if ($this->pageConfig->getTitle()->get()) {
            $pageContentHeading = $this->pageConfig->getTitle()->get();
        } else {
            $pageContentHeading = $productName;
        }

        $json = array(
            "@context" => "http://schema.org",
            "@type" => "Article",
            "author" => $siteName,
            "image" => array(
                '@type' => 'ImageObject',
                'url' => $productImage,
                'width' => self::PRODUCT_IMAGE_WIDTH,
                'height' => self::PRODUCT_IMAGE_HEIGHT,
            ),
            "name" => $productName,
            "description" => $productShortDescription,
            "datePublished" => $_product->getCreatedAt(),
            "dateModified" => $_product->getUpdatedAt(),
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