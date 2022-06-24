<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Products;

/**
 * Main contact form block
 */
class ProductItem extends \Magento\Catalog\Block\Product\AbstractProduct
{
	/**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;
	
	/**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
	
	
    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
		\Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
		$this->urlHelper = $urlHelper;
		$this->formKey = $formKey;
        parent::__construct(
            $context,
            $data
        );
    }
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
	
	/**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}

