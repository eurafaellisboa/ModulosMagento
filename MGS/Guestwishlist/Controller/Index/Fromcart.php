<?php

namespace MGS\Guestwishlist\Controller\Index;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Fromcart extends \Magento\Framework\App\Action\Action {
    /**
     * @var CheckoutCart
     */
    protected $cart;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var MGS\Guestwishlist\Helper\Data
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
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param CheckoutCart $cart
     * @param CartHelper $cartHelper
     * @param Escaper $escaper
     * @param \MGS\Guestwishlist\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        CheckoutCart $cart,
        CartHelper $cartHelper,
        Escaper $escaper,
        \MGS\Guestwishlist\Helper\Data $helper, 
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, 
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cart = $cart;
        $this->cartHelper = $cartHelper;
        $this->escaper = $escaper;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }
    
    
    /**
     * Add cart item to wishlist and remove from cart
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $quoteItemId = (int)$this->getRequest()->getParam('id');
            $quoteItem = $this->cart->getQuote()->getItemById($quoteItemId);
            if (!$quoteItem) {
                throw new LocalizedException(
                    __('The requested cart item doesn\'t exist.')
                );
            }

            $itemId = $this->_helper->getRandomString();
            $buyRequest = $quoteItem->getBuyRequest();
            $product = $quoteItem->getProduct();
            $item = [
                'item_id' => $itemId,
                'qty' => 1,
                'info_buyRequest' => $buyRequest->getData()
            ];
            if ($product->getTypeId() === 'configurable' && $buyRequest->getSuperAttribute()) {
                $item['super_attribute'] = $buyRequest->getSuperAttribute();
            }
            $cookie = $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME);
            //echo '<pre>';print_r($item);
            //echo '<pre>';print_r($cookie);die();
            //echo $product->getEntityId();die();
            /*
             * check if existing product with same options
             * if yes, we don't need add to wishlist
             */
            if (!$this->_helper->checkExistItem($product->getEntityId(), $item, $cookie)) {
                $cookie[$product->getEntityId()][$itemId] = $item;
                $metadata = $this->_cookieMetadataFactory
                        ->createPublicCookieMetadata()
                        ->setPath('/')
                        ->setDuration(86400);
                $this->_cookieManager->setPublicCookie(
                        \MGS\Guestwishlist\Helper\Data::COOKIE_NAME, serialize($cookie), $metadata
                );
            }

            $this->cart->getQuote()->removeItem($quoteItemId);
            $this->cart->save();

            $this->messageManager->addSuccessMessage(__(
                "%1 has been moved to your wish list.",
                $this->escaper->escapeHtml($product->getName())
            ));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t move the item to the wish list.'));
        }
        return $resultRedirect->setUrl($this->cartHelper->getCartUrl());
    }

}
