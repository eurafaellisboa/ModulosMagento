<?php

namespace Blueskytechco\QuickviewProduct\Block\Product;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;

class View extends AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface
{
    protected $string;
    protected $_jsonEncoder;
    protected $priceCurrency;
    protected $urlEncoder;
    protected $_productHelper;
    protected $productTypeConfig;
    protected $_localeFormat;
    protected $customerSession;
    protected $productRepository;
    protected $_imageHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        array $data = []
    ) {
    	$this->imageHelper = $imageFactory;
        $this->_productHelper = $productHelper;
        $this->urlEncoder = $urlEncoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->productTypeConfig = $productTypeConfig;
        $this->string = $string;
        $this->_localeFormat = $localeFormat;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $data
        );
    }

    public function resizeImage($product, $typeImage, $width, $height)
    {
        $imageHelper = $this->imageHelper->create();
        $imageHelper->init($product, $typeImage)->setImageFile($product->getData($typeImage))->resize($width, $height);
        return $imageHelper->getUrl();
    }

    public function getProduct()
    {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    public function getIdentities()
    {
        $product = $this->getProduct();

        return $product ? $product->getIdentities() : [];
    }
}