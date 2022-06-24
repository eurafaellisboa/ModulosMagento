<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Guestwishlist\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var MGS\Guestwishlist
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
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \MGS\Guestwishlist\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \MGS\Guestwishlist\Helper\Data $helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * Adding new item
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
		$errorMessage = __('We can\'t add the item to Wish List right now.');
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage($errorMessage);
            return;
        }
		
        $requestParams = $this->getRequest()->getParams();
        if(isset($requestParams['guest'])){
            $checkGuest = (int)$requestParams['guest'];
        }else {
            $checkGuest = 1;
        }
        
        if($checkGuest == 1){
            $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
            if (!$productId) {
				$this->messageManager->addErrorMessage($errorMessage);
                return;
            }
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if (!$product || !$product->isVisibleInCatalog()) {
                $this->messageManager->addErrorMessage($errorMessage);
                return;
            }

            try {
                $itemId = $this->_helper->getRandomString();
                $values = array();
				$qty = 1;
				
                if(isset($requestParams['buyRequest']) && $requestParams['buyRequest'] != ''){
                    parse_str($requestParams['buyRequest'], $values);
					unset($values['product'], $values['uenc'], $values['form_key']);
                    $buyRequest = new \Magento\Framework\DataObject($values);
                }
                
                $item = [
                    'item_id' => $itemId,
                    'qty' => $qty,
                    'description' => '',
					'info_buyRequest' => $requestParams
                ];
                
                if(isset($buyRequest['qty'])){
                    $item['qty'] = $buyRequest['qty'];
                    $qty = $buyRequest['qty'];
                }
                
				if ($product->getTypeId() === 'configurable' && isset($buyRequest['super_attribute']) && is_array($buyRequest['super_attribute'])) {
					$item['super_attribute'] = $buyRequest['super_attribute'];
				}
                $cookie = $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME);
				
                if (!$this->_helper->checkExistItem($productId, $item, $cookie)) {
                    $cookie[$productId][$itemId] = $item;
                }else {
					$itemIdRemove = key($cookie[$productId]);
					$item['qty'] = $qty + $cookie[$productId][$itemIdRemove]['qty'];
					$cookie = $this->removeItemById($itemIdRemove, $cookie);
					$cookie[$productId][$itemId] = $item;
				}
				
				$metadata = $this->_cookieMetadataFactory
						->createPublicCookieMetadata()
						->setPath('/')
						->setDuration(86400);
				$this->_cookieManager->setPublicCookie(
						\MGS\Guestwishlist\Helper\Data::COOKIE_NAME, serialize($cookie), $metadata
				);
                
				$this->messageManager->addSuccess(__('%1 has been added to your Wish List.', $product->getName()));
				
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                        __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addException($e, $errorMessage);
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }else {
			
            $wishlist = $this->wishlistProvider->getWishlist();
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }

            $session = $this->_customerSession;
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }

            $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
            if (!$productId) {
                $this->messageManager->addErrorMessage($errorMessage);
                return;
            }

            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if (!$product || !$product->isVisibleInCatalog()) {
                $this->messageManager->addErrorMessage($errorMessage);
                return;
            }

            try {
                $values = array();
				if(isset($requestParams['buyRequest'])){
					parse_str($requestParams['buyRequest'], $values);
				}else {
					$values['qty'] = 1;
				}
				
				$buyRequest = new \Magento\Framework\DataObject($values);
				$result = $wishlist->addNewItem($product, $buyRequest);
				
				if (is_string($result)) {
					throw new \Magento\Framework\Exception\LocalizedException(__($result));
				}
				
                $wishlist->save();
				
				$this->_eventManager->dispatch(
					'wishlist_add_product',
					['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
				);
				
                $referer = $session->getBeforeWishlistUrl();
                if ($referer) {
                    $session->setBeforeWishlistUrl(null);
                } else {
                    $referer = $this->_redirect->getRefererUrl();
                }

                $this->_objectManager->get('Magento\Wishlist\Helper\Data')->calculate();
                
                $this->messageManager->addSuccess(__('%1 has been added to your Wish List.', $product->getName()));
                
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $errorMessage);
            }
        }
        
        return;
    }
	
	/**
     * 
     * @param string $itemId
     * @return [] array of wishlist
     */
    protected function removeItemById($itemId, $wishlist) {
        if ($wishlist !== null && is_array($wishlist)) {
            foreach ($wishlist as $productId => $items) {
                foreach ($items as $key => $_item) {
                    if ($itemId == $key) {
                        unset($wishlist[$productId][$itemId]);
                        // clean empty parent
                        // unset parent if does not have any child products
                        if (empty($wishlist[$productId])) {
                            unset($wishlist[$productId]);
                        }
                    }
                }
            }
        }
        return $wishlist;
    }
}
