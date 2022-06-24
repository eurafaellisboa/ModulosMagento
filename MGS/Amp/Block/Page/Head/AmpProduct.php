<?php

namespace MGS\Amp\Block\Page\Head;

use MGS\Amp\Block\Page\Head\AmpAbstract as AmpAbstract;

class AmpProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{
	/**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;
	
	/**
     * @var \MGS\Amp\Helper\Config 
     */
    protected $_configHelper;
	

	/**
     * Construct
     *
     * @param \Magento\Catalog\Block\Product\Context $context,
     * @param \MGS\Amp\Helper\Config $configHelper,
     * @param \Magento\Catalog\Helper\Product $productHelper,
     * @param array $data
     */
    public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
        \MGS\Amp\Helper\Config $configHelper,
        \Magento\Catalog\Helper\Product $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_productHelper = $productHelper;
        $this->_configHelper = $configHelper;
    }

	/**
	 * Retrieve additional data
	 * @return array
	 */
    public function getAmpParams() {
        $params = parent::getAmpParams();
        $_product = $this->getProduct();

        return array(
            'type' => 'product',
            'url' => $this->_configHelper->getCanonicalUrl($_product->getProductUrl()),
            'image' => $this->getImage($_product, 'product_page_image_large', [])->getData('image_url'),
            'title' => $this->pageConfig->getTitle()->get(),
            'description' => mb_substr($this->pageConfig->getDescription(), 0, 200, 'UTF-8'),
        );
    }

	/**
     * {@inheritDoc}
     */
    protected function _prepareLayout()
    {
        $product = $this->getProduct();

        if ($this->_productHelper->canUseCanonicalTag()) {
            $productUrl = $product->getUrlModel()->getUrl(
                $product,
                ['_ignore_category' => true]
            );
        } else {
            $productUrl = $product->getUrl();
        }

        $this->pageConfig->addRemotePageAsset(
            $this->_configHelper->getCanonicalUrl($productUrl),
            'canonical',
            ['attributes' => ['rel' => 'canonical']],
            AmpAbstract::DEFAULT_ASSET_NAME
        );

        return parent::_prepareLayout();
    }
}
