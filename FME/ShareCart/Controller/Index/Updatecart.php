<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Controller\Index;

use FME\ShareCart\Model\Sharecart;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Catalog\Model\ProductFactory;

class Updatecart extends \Magento\Framework\App\Action\Action
{
    
    protected $jsonFactory;
    protected $scopeConfig;
    protected $_transportBuilder;
    protected $storeManager;
   public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        ProductFactory $product,
        QuoteFactory $quoteFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \FME\ShareCart\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
             $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->jsonFactory = $jsonFactory;
        $this->helper=$helper;
        $this->_checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
        $this->request = $request;
                           //	$this->cart = $cart;
   	        $this->product = $product;
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

   
    public function saveQuatity($QuoteId,$itemId, $qty)
    {
        $quote = $this->quoteRepository->getActive($QuoteId);
        $quoteItem = $quote->getItemById($itemId);
       
        $quoteItem->setQty((double) $qty);
        $quoteItem->save();
        $this->quoteRepository->save($quote);
    }
    public function execute()
    {

        $postProduct = (array) $this->getRequest()->getPost("product_id");
        $postQuantity = (array) $this->getRequest()->getPost("quantity");
        $quote_id = (String) $this->getRequest()->getPost("quote_id");
        $post = (array) $this->getRequest()->getPost();
       // print_r($post);exit;
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $items = $quote->getAllVisibleItems();

        foreach($items as $item) {

            $qty=$this->getProductQuantity($item->getProductId(),$postProduct,$postQuantity);
            
            //echo $qty;
            if($qty<=0)
            {
                $item->delete();
            }else{


                //$item->getProductId()
                $this->saveQuatity($quote_id,$item->getItemId(),$qty);
                //$item->setQty($qty);
               //$item->save();
            }      
        }

        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteFactory =$this->quoteFactory;
        $currentQuoteObj = $quoteFactory->create()->load($quote_id);
        $currentQuoteObj->setIsActive(false)->save();
                $this->_checkoutSession->clearQuote()->clearStorage();
                $this->_checkoutSession->clearQuote();
                $this->_checkoutSession->clearStorage();
                $this->_checkoutSession->clearHelperData();
                $this->_checkoutSession->resetCheckout();
                $this->_checkoutSession->restoreQuote();
       // $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $this->helper;
        $quote_id=$helper->my_simple_crypt($quote_id,"e");
        $resultRedirect->setPath('sharecart/index/editmycart?quote_id='.(String)$quote_id);
        return $resultRedirect;

    }
    public function getProductQuantity($productId,$product_Post,$quantity_Post)
    {
        $qty=0;
        foreach ($product_Post as $key => $value) {
            if($value==$productId)
            {
                $qty=$quantity_Post[$key];
            }
        }
        return $qty;
    }

    
}
