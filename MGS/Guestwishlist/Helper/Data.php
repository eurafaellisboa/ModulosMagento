<?php

namespace MGS\Guestwishlist\Helper;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Api\FilterBuilder;

class Data extends AbstractHelper {

    const COOKIE_NAME = 'guestwishlist';

    /**
     * Customer Wishlist instance
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;
    
    /**
     * @var ImageBuilder
     */
    protected $imageBuilder;
    
    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;
    
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;
    
    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postHelper;
    
    /**
     * @var Magento\Customer\Model\Session
     */
    protected $customerSession;
	
	protected $_layoutFactory;
	
    protected $_jsonEncoder;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @codeCoverageIgnore
     */
    public function __construct(
		\Magento\Framework\App\Helper\Context $context, 
		\Magento\Store\Model\StoreManagerInterface $storeManager, 
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository, 
		SearchCriteriaBuilder $searchCriteriaBuilder, 
		FilterBuilder $filterBuilder, 
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, 
		\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
		\Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
		\Magento\Checkout\Helper\Data $checkoutHelper,
		\Magento\Checkout\Helper\Cart $cartHelper,
		\Magento\Framework\Data\Helper\PostHelper $postHelper,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		EncoderInterface $jsonEncoder,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
    ) {
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->imageBuilder = $imageBuilder;
		$this->_layoutFactory = $layoutFactory;
        $this->checkoutHelper = $checkoutHelper;
        $this->cartHelper = $cartHelper;
        $this->postHelper = $postHelper;
        $this->customerSession = $customerSession;
        $this->_jsonEncoder = $jsonEncoder;
		$this->wishlistProvider = $wishlistProvider;
        parent::__construct($context);
    }

    /**
     * 
     * @return int|boolean
     */
    public function isModuleEnable() {
        return $this->scopeConfig->isSetFlag(
                        'guestwishlist/additional/enable_module', ScopeInterface::SCOPE_STORE
        );
    }
	
	public function getAjaxWishlistInitOptions()
    {
        $options = [
            'ajaxWishlist' => [
                'enabled' => true
            ],
        ];
        return $this->_jsonEncoder->encode($options);
    }

    /**
     * 
     * @return int|boolean
     */
    public function isAllowMergeItemAfterLogin() {
        return $this->scopeConfig->isSetFlag(
                        'guestwishlist/additional/merge_after_login', ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * 
     * @return Magento/Customer/Model/Customer
     */
    public function getCustomer() {
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->_objectManager->get('Magento/Customer/Model/Customer')->load($customerId);
        return $customer;
    }
    
    /**
     * Check is allow wishlist action in shopping cart
     *
     * @return bool
     */
    public function isAllowInCart() {
        $allowInCart = $this->scopeConfig->isSetFlag(
                        'guestwishlist/additional/move_from_cart', ScopeInterface::SCOPE_STORE
        );
        return $allowInCart && $this->isModuleEnable() && !$this->isLoggedIn();
    }
    
    /**
     * 
     * @return boolean
     */
    public function isLoggedIn() {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist() {
        if ($this->_wishlist === null) {
			if($this->isLoggedIn()){
				$currentUserWishlist = $this->wishlistProvider->getWishlist();
				if ($currentUserWishlist) {
					$this->_wishlist = $currentUserWishlist->getItemCollection();
				}
			}else {
				$list = $this->getCookie(self::COOKIE_NAME);
				
				if ($list !== null) {
					$this->_wishlist = $list;
				}
			}
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve wishlist items collection
     *
     * @return array []
     */
    public function getWishlistItemCollection($limit = null, $wishlistCookie = NULL) {
		$wishlist = [];
		if($wishlistCookie !== null){
			$wishlist = $wishlistCookie;
		}else {
			if ($this->getWishlist() !== null) {
				$wishlist = $this->getWishlist();
			}
		}
		if($this->isLoggedIn()){
			$wishlist->clear()->setPageSize($limit)
            ->setInStockFilter(true)->setOrder('added_at');

			$items = [];
			foreach ($wishlist as $wishlistItem) {
				$items[] = $wishlistItem;
			}
			return $items;
		}
        if ($wishlist !== null) {
            $productIds = $this->getProductIds($limit, $wishlist);
            if (count($productIds)) {
                $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in');
                $searchCriteriaBuilder = $this->searchCriteriaBuilder->create();
                $searchResults = $this->productRepository->getList($searchCriteriaBuilder);
                $productCollection = $searchResults->getItems();
                $items = [];
                if (count($productCollection)) {
                    foreach ($productCollection as $_product) {
                        foreach ($wishlist[$_product->getId()] as $_cookieItem) {
                            $deleteParams = $this->getDeleteItemParams($_cookieItem, $_product, $_product->getName());
                            $_cookieItem['product'] = $_product;
                            if (isset($_cookieItem['super_attribute'])) { // configurable product
                                $_cookieItem['wishlist_options'] = $this->getItemOptionsHtml($_product, $_cookieItem['super_attribute']);
                            }
                            $_cookieItem['data_post'] = $this->getItemCartParams($_cookieItem);
                            $_cookieItem['data_delete'] = $deleteParams;
                            $items[] = $_cookieItem;
                        }
                    }
                }
                return $items;
            }
        }
        return [];
    }
	
    
    /**
     * Retrieve wishlist items count
     *
     * @return int
     */
    public function getItemCount() {
        return count($this->getWishlistItemCollection());
    }
    
    /**
     * Retrieve product ids as array
     * 
     * @param int $limit
     * @param array $wishlist
     *      *
     * @return array []
     */
    protected function getProductIds($limit, $wishlist) {
        $productIds = [];
        $count = 0;
        if (is_array($wishlist) && count($wishlist)) {
            foreach ($wishlist as $_productId => $items) {
                if(empty($items)) {
                    continue;
                }
                if ($limit !== null && $count >= $limit) {
                    break;
                }
                $productIds[] = $_productId;
                $count++;
            }
        }
        return $productIds;
    }

    /**
     * 
     * @param type $product
     * @param [] $optionArray
     * @return string
     */
    protected function getItemOptionsHtml($product, $optionArray) {
        $html = '';
        $typeInstance = $product->getTypeInstance();
        $attributeCollection = $typeInstance->getConfigurableAttributesAsArray($product);
        foreach ($attributeCollection as $attribute) {
            $attributeLabel = $attribute['store_label'];
            $attributeOptions = $attribute['options'];
            if (is_array($attributeOptions) && isset($optionArray[$attribute['attribute_id']])) {
                foreach ($attributeOptions as $option) {
                    if ($option['value'] == $optionArray[$attribute['attribute_id']]) {
                        $html .= '<dl>
                                    <dt class="label">' . $attributeLabel . '</dt>
                                    <dd class="values">
                                        <span>' . $option['label'] . '</span>
                                    </dd>
                                </dl>';
                    }
                }
            }
        }
        return $html;
    }
    
    /**
     * 
     * @return []
     */
    public function getAddAllCartParams() {
        return $this->postHelper->getPostData($this->_getUrlStore()->getUrl('guestwishlist/index/all'), []);
    }
    
    /**
     * 
     * @param [] $item
     * @return []
     */
    public function getItemCartParams($item) {
        $product = $item['product'];
        $params = ['product' => $product->getEntityId()];
        if (isset($item['super_attribute'])) {
            foreach($item['super_attribute'] as $attributeId => $value) {
                $params['super_attribute[' . $attributeId . ']'] = $value;
            }
        }
        
        $params = $this->addRefererToParams($params);
        return $this->postHelper->getPostData($this->_getUrlStore($product)->getUrl('guestwishlist/cart/add'), $params);
    }
    
    /**
     * 
     * @param [] $item
     * @param Magento\Catalog\Model\Product $product
     * @param boolean $removeAll
     * @return []
     */
    public function getDeleteItemParams($item, $product, $productName) {
        $url = $this->_getUrlStore($product)->getUrl('guestwishlist/index/remove');
        $params = [];
		$params['itemId'] = $item['item_id'];
		$params['productName'] = $productName;
        
        return $this->postHelper->getPostData($url, $params);
    }
    
    /**
     * Retrieve Item Store for URL
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item
     * @return \Magento\Store\Model\Store
     */
    protected function _getUrlStore($item = null) {
        $storeId = null;
        $product = null;
        if ($item instanceof \Magento\Wishlist\Model\Item) {
            $product = $item->getProduct();
        } elseif ($item instanceof \Magento\Catalog\Model\Product) {
            $product = $item;
        }
        if ($product) {
            if ($product->isVisibleInSiteVisibility()) {
                $storeId = $product->getStoreId();
            } else {
                if ($product->hasUrlDataObject()) {
                    $storeId = $product->getUrlDataObject()->getStoreId();
                }
            }
        }
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Add UENC referer to params
     *
     * @param array $params
     * @return array
     */
    public function addRefererToParams(array $params) {
        $params[ActionInterface::PARAM_NAME_URL_ENCODED] =
            $this->urlEncoder->encode($this->_getRequest()->getServer('HTTP_REFERER'));
        return $params;
    }

    /**
     * @param self::COOKIE_NAME $name
     */
    public function getCookie($name) {
        $cookie = $this->_cookieManager->getCookie($name);
        if ($cookie) {
            $cookie = unserialize($cookie);
        }
        return $cookie;
    }
    
    public function formatPrice($data){
        return $this->checkoutHelper->formatPrice($data);
    }
    
    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }    

    /**
     * 
     * @param int $length default 8
     * @return string
     */
    public function getRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;
    }
    
    /**
     * 
     * @param int $productId
     * @param [] $newItem
     * @param [] $wishlist
     * @return boolean
     */
    public function checkExistItem($productId, $newItem, $wishlist) {
        if ($wishlist !== null && is_array($wishlist)) {
            foreach ($wishlist as $key => $items) {
                if ($key == $productId) {
                    foreach ($items as $_item) {
                        if (!isset($_item['super_attribute']) && !isset($newItem['super_attribute'])) {
                            return true;
                        }
                        if (isset($_item['super_attribute']) && isset($newItem['super_attribute']) 
                                && $_item['super_attribute'] == $newItem['super_attribute']) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
	
	
	public function getWishlistJson($productCollection){
		$blockWishlistAdd = $this->_layoutFactory->create()->createBlock('Magento\Wishlist\Block\AddToWishlist');
		
		return $blockWishlistAdd->getWishlistOptions($productCollection);
	}

}
