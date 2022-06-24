<?php

namespace MGS\Guestwishlist\Controller\Index;

use Magento\Checkout\Model\Cart as CustomerCart;
class All extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var BelVG\GuestWishlist\Helper\Data
     */
    protected $_helper;
    
    /**
    * @var \Magento\Framework\Stdlib\CookieManagerInterface
    */
    protected $_cookieManager;
    
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param \BelVG\GuestWishlist\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \MGS\Guestwishlist\Helper\Data $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }
    
    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result = [];
        try {
            $cookie = $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME) != null 
                    ? $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME) : [];
            
            if (count($cookie)) {
                $addedProducts = $this->addProductsToCart($cookie);
            }
            
            //make message for guest wishlist
            if(count($addedProducts)) {
                $products = [];
                foreach ($addedProducts as $_product) {
                    /** @var $product \Magento\Catalog\Model\Product */
                    $products[] = '"' . $_product['name'] . '"';
                }
                
                $this->messageManager->addSuccess(
                    __('%1 product(s) have been added to shopping cart: %2.', count($addedProducts), join(', ', $products))
                );
                
                // save cart and collect totals
                $this->cart->save()->getQuote()->collectTotals();
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_url->getUrl('guestwishlist'));
        return $resultRedirect;
    }
    
    /**
     * Adding products to cart
     *
     * @param [] $wishlist
     * @return $this
     */
    protected function addProductsToCart($wishlist) {
        $addedProduct = [];
        $allAvailable = true;
        if (is_array($wishlist) && count($wishlist)) {
            foreach ($wishlist as $productId => $items) {
                if (count($items)) {
                    $product = $this->_getProduct($productId);
                    foreach ($items as $key => $_item) {
                        if ($product->getId() && $product->isVisibleInCatalog()) {
                            try {
                                if($product->isSalable()) {
                                    $this->cart->addProduct($product, $_item);
                                    $addedProduct[] = [
                                        'name' => $product->getName(),
                                        'item_id' => $_item['item_id']
                                    ];
                                    //unset($wishlist[array_search($_item, $wishlist)]);
                                } else {
                                    $this->messageManager->addError(__('%1 is not salable.', $product->getName()));
                                }                        
                            } catch (\Exception $e) {
                                $this->messageManager->addError(__('%1 for "%2".', trim($e->getMessage(), '.'), $product->getName()));
                            }
                        } else {
                            $allAvailable = false;
                        }
                    }
                }
            }
//            if(count($addedProduct)) {
//                $metadata = $this->_cookieMetadataFactory
//                    ->createPublicCookieMetadata()
//                    ->setPath('/')
//                    ->setDuration(86400);
//                $this->_cookieManager->setPublicCookie(
//                    \MGS\Guestwishlist\Helper\Data::COOKIE_NAME,
//                    serialize($wishlist),
//                    $metadata
//                );
//            }

            if (!$allAvailable) {
                $this->messageManager->addError(__("We don't have some of the products you want."));
            }
        }
        return $addedProduct;
    }


    /**
     * Get product object based on requested product information
     *
     * @param   Product|int|string $productInfo
     * @return  Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProduct($productInfo)
    {
        $product = null;
        $storeId = $this->_storeManager->getStore()->getId();
        try {
            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($productInfo);
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'), $e);
        }
        return $product;
    }
}