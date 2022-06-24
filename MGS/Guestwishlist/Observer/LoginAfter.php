<?php

namespace MGS\Guestwishlist\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;

class LoginAfter implements ObserverInterface {

    /**
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     *
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface 
     */
    protected $wishlistProvider;
    
    /**
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface 
     */
    protected $productRepository;
    
    /**
     *
     * @var \MGS\Guestwishlist\Helper\Data 
     */
    protected $_helper;


    /**
     * 
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param \MGS\Guestwishlist\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        \MGS\Guestwishlist\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) {
        if (!$this->_helper->isAllowMergeItemAfterLogin()) {
            return;
        }
        $wishlist = $this->wishlistProvider->getWishlist();
        $cookie = $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME);
        
        if ($cookie) {
            foreach ($cookie as $productId => $items) {
                try {
                    $product = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                    $product = null;
                }

                if ($product !== null) {
                    foreach ($items as $_item) {
                        $requestParams = $_item['info_buyRequest'];
                        $requestParams['qty'] = isset($_item['qty']) ? $_item['qty'] : 1;
                        $buyRequest = new \Magento\Framework\DataObject($requestParams);

                        $result = $wishlist->addNewItem($product, $buyRequest);
                        if (is_string($result)) {
                            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($result);
                        }
                        $wishlist->save();
                    }
                }
            }
        }
    }

}
