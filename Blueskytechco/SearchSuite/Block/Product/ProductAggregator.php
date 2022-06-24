<?php
/**
 * Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\SearchSuite\Block\Product;

use \Blueskytechco\SearchSuite\Block\Product as ProductBlock;
use \Magento\Catalog\Helper\Output as CatalogHelperOutput;
use \Magento\Catalog\Block\Product\ReviewRendererInterface;
use \Magento\Framework\Stdlib\StringUtils;
use \Magento\Framework\Url\Helper\Data as UrlHelper;
use \Magento\Framework\Data\Form\FormKey;
use \Magento\Framework\View\Asset\Repository;
use \Magento\Framework\Escaper;
use Magento\Catalog\Helper\ImageFactory;
/**
 * ProductAggregator class for autocomplete data
 *
 * @method Product setProduct(\Magento\Catalog\Model\Product $product)
 */
class ProductAggregator extends \Magento\Framework\DataObject
{ 
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

	/**
     * @var \Blueskytechco\SearchSuite\Block\Product
     */
    protected $productBlock;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var CatalogHelperOutput
     */
    protected $catalogHelperOutput;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * ProductAgregator constructor.
     *
     * @param ImageFactory $imageFactory
     * @param ProductBlock $productBlock
     * @param StringUtils $string
     * @param UrlHelper $urlHelper
     * @param Repository $assetRepo
     * @param CatalogHelperOutput $catalogHelperOutput
     * @param FormKey $formKey
     * @param Escaper $escaper
     */
    public function __construct(
        ImageFactory $imageFactory,
        ProductBlock $productBlock,
        StringUtils $string,
        UrlHelper $urlHelper,
        Repository $assetRepo,
        CatalogHelperOutput $catalogHelperOutput,
        FormKey $formKey,
        Escaper $escaper
    ) {
        $this->imageFactory        = $imageFactory;
        $this->productBlock        = $productBlock;
        $this->string              = $string;
        $this->urlHelper           = $urlHelper;
        $this->assetRepo           = $assetRepo;
        $this->catalogHelperOutput = $catalogHelperOutput;
        $this->formKey             = $formKey;
        $this->escaper             = $escaper;
    }
	
    /**
     * Set product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->product = $product;
        return $this;
    }
	
	/**
     * Retrieve product name
     *
     * @return string
     */
    public function getName()
    {
        return strip_tags(html_entity_decode($this->product->getName()));
    }

    /**
     * Retrieve product sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->product->getSku();
    }

    /**
     * Retrieve product small image url
     *
     * @return bool|string
     */
    public function getSmallImage()
    {
        $product   = $this->product;
        $image = $this->imageFactory->create()->init($product, 'product_small_image');
        return $image->getUrl();
    }

    /**
     * Retrieve product reviews rating html
     *
     * @return string
     */
    public function getReviewsRating()
    {
        return $this->productBlock->getReviewsSummaryHtml(
            $this->product,
            ReviewRendererInterface::SHORT_VIEW,
            true
        );
    }

    /**
     * Retrieve product short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        $shortDescription = $this->product->getShortDescription();

        return $this->cropDescription($shortDescription);
    }
	
	/**
     * Crop description to 50 symbols
     *
     * @param string|html $data
     * @return string
     */
    protected function cropDescription($data)
    {
        if (!$data) {
            return '';
        }

        $data = strip_tags($data);
        $data = (strlen($data) > 50) ? $this->string->substr($data, 0, 50) . '...' : $data;

        return $data;
    }


    /**
     * Retrieve product price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->productBlock->getProductPrice(
            $this->product,
            \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE
        );
    }

    /**
     * Retrieve product url
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->productBlock->getProductUrl($this->product);
    }

    /**
     * Retrieve product add to cart data
     *
     * @return array
     */
    public function getAddToCartData()
    {
        $formUrl             = $this->productBlock->getAddToCartUrl(
            $this->getProduct(),
            ['mageworx_searchsuiteautocomplete' => true]
        );
        $productId           = $this->product->getEntityId();
        $paramNameUrlEncoded = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;
        $urlEncoded          = $this->urlHelper->getEncodedUrl($formUrl);
        $formKey             = $this->formKey->getFormKey();

        $addToCartData = [
            'formUrl'             => $formUrl,
            'productId'           => $productId,
            'paramNameUrlEncoded' => $paramNameUrlEncoded,
            'urlEncoded'          => $urlEncoded,
            'formKey'             => $formKey
        ];

        return $addToCartData;
    }
}
