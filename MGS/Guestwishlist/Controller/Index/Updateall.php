<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Guestwishlist\Controller\Index;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Updateall extends \Magento\Framework\App\Action\Action
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
     * @var JsonFactory $resultJsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \MGS\Guestwishlist\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param ProductRepositoryInterface $productRepository
	 * @param JsonFactory $resultJsonFactory
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
		JsonFactory $resultJsonFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    ) {
        $this->_customerSession = $customerSession;
        $this->wishlistProvider = $wishlistProvider;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->formKeyValidator = $formKeyValidator;
		$this->resultJsonFactory = $resultJsonFactory;
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
        $cookie = $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME);
		$_isAjax = false;
		$post = $this->getRequest()->getPostValue();
		if ($post && isset($post['is_ajax']) && $post['is_ajax'] == 1) {
			$_isAjax = true;
			/** @var \Magento\Framework\Controller\Result\Json $resultJson */
			$resultJson = $this->resultJsonFactory->create();
			$layout = $this->_view->getLayout();
			$block = $layout->createBlock(\Magento\Framework\View\Element\Template::class);
			$block->setTemplate('MGS_Guestwishlist::list.phtml');
		}
		if(!$_isAjax){
			$resultRedirect = $this->resultRedirectFactory->create();
			if (!$this->formKeyValidator->validate($this->getRequest())) {
				$resultRedirect->setPath('*/*/');
				return $resultRedirect;
			}
		}else {
			if (!$this->formKeyValidator->validate($this->getRequest())) {
				$this->messageManager->addErrorMessage(__('Something when wrong. Can\'t update wishlist right now.'));
				$block->setWishlistParam($cookie);
				$response = [
					'content' => $block->toHtml(),
				];
				return $resultJson->setData($response);
			}
		}
        if ($post && isset($post['item_id']) && is_array($post['item_id'])) {
            foreach ($post['item_id'] as $key => $item_id) {
				$itemQty = $post['qty'][$key] ? $post['qty'][$key] : 0;
				$itemDescription = $post['description'][$key] ? $post['description'][$key] : "";
				if($itemQty == 0){
					$cookie = $this->removeItemById($item_id, $cookie);
				}else {
					$oldQty = $cookie[$key][$item_id]['qty'];
					$oldDescription = $cookie[$key][$item_id]['description'];
					if($oldQty != $itemQty || $oldDescription != $itemDescription){
						$cookie = $this->removeItemById($item_id, $cookie);
						
						$newItem = array(
							"item_id" => $item_id,
							"qty" => $itemQty,
							"description" => $itemDescription
						);
						$cookie[$key][$item_id] = $newItem;
					}
				}
            }
        }
		
		$metadata = $this->_cookieMetadataFactory
				->createPublicCookieMetadata()
				->setPath('/')
				->setDuration(86400);
		$this->_cookieManager->setPublicCookie(
				\MGS\Guestwishlist\Helper\Data::COOKIE_NAME, serialize($cookie), $metadata
		);
            
		$this->messageManager->addSuccess(__('All Items has been updated.'));
		if(!$_isAjax){
			$resultRedirect->setUrl($this->_url->getUrl('guestwishlist'));
			return $resultRedirect;
		}else{
			$block->setWishlistParam($cookie);
			$response = [
                'content' => $block->toHtml(),
            ];
			return $resultJson->setData($response);
		}
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
