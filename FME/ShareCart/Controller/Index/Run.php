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

class Run extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) { 
             $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->jsonFactory = $jsonFactory;
        $this->cart = $cart;

        $this->_cacheTypeList = $cacheTypeList;
        $this->_checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->request = $request;
                           //	$this->cart = $cart;
   	        $this->product = $product;
        $this->quoteRepository = $quoteRepository;

    $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    public function flushCache()
    {
    $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        } 
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }  
    }
    public function addLinkItemToCart($id)
    {

        if($id > 0)
        {
            $quote = $this->quoteFactory->create()->load($id);
            $items = $quote->getAllVisibleItems();
            
            foreach ($items as $item)
            {
                $productId =$item->getProductId();
                $_product = $this->product->create()->load($productId); 
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                $info = $options['info_buyRequest'];
                            $request1 = new \Magento\Framework\DataObject();
                            $request1->setData($info);
                            
                            $this->cart->addProduct($_product, $request1);
                            
            }
            $this->cart->save();   
        }
        return;
    }
    public function execute()
    {
        

        $this->request->getParams(); // all params
        $quote_id=$this->request->getParam('quote_id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $this->_objectManager->create('FME\ShareCart\Helper\Data');
        $isclean=$this->request->getParam('clean');
        $quote_id=$this->request->getParam('quote_id');
        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $url=$helper->getBaseUrl();
        if($isclean=="1")
        {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
                $cartObject = $objectManager->create('Magento\Checkout\Model\Cart')->truncate(); 
                $cartObject->saveQuote();
                $this->flushCache();
                $url=$url."sharecart/index/run?quote_id=".$quote_id."&clean=2";
                $result->setUrl($url);
                return $result;
        }
        if(strlen($quote_id)>0)
        {
            $quote_id=$helper->my_simple_crypt($quote_id,"d");
        }
        if($isclean=="2")
        {
            $id = $quote_id;                
            if($id > 0)
            {
                $quote = $this->quoteFactory->create()->load($id);
                $items = $quote->getAllVisibleItems();
                
                foreach ($items as $item)
                {
                    $productId =$item->getProductId();
                    $_product = $this->product->create()->load($productId); 
                    $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                    $info = $options['info_buyRequest'];
                                $request1 = new \Magento\Framework\DataObject();
                                $request1->setData($info);
                                
                                $this->cart->addProduct($_product, $request1);
                                
                }
                $this->cart->save();   
            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('checkout/cart/');
            return $resultRedirect;
           
        }
       
        if($helper->isUserRestict() && $helper->getCurrentCartItem()!="" && $helper->getCurrentCartItem()==$quote_id)
        {

            //echo "asdasd";exit;
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('checkout/cart/');
            return $resultRedirect;
           
        }
        else if($helper->isUserRestict() && $helper->getCurrentCartItem()!="" && $helper->getCurrentCartItem()!=$quote_id)    
        {
            
            // print_r($helper->getCurrentCartItem());
            // echo "here";exit;
            $this->_view->loadLayout(); 
            $this->_view->renderLayout(); 
            return;
        }
        else{
          //  echo "asdasd";exit;
            try{
                $id = $quote_id;                
                if($id > 0)
                {
                    $quote = $this->quoteFactory->create()->load($id);
                    $items = $quote->getAllVisibleItems();
                    
                    foreach ($items as $item)
                    {
                        $productId =$item->getProductId();
                        $_product = $this->product->create()->load($productId); 
                        $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                        $info = $options['info_buyRequest'];
                                    $request1 = new \Magento\Framework\DataObject();
                                    $request1->setData($info);
                                    
                                    $this->cart->addProduct($_product, $request1);
                                    
                    }
                    $this->cart->save();   
                }
                
            }
            catch (\Exception $e)
            {
                $this->messageManager->addError( __($e->getMessage()) );
            }
           	 

        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/cart/');
        return $resultRedirect;


        
       // print_r( $this->request->getParam('quote_id'));
        //echo "Hi";
        //exit;
               /* $this->_checkoutSession->clearQuote()->clearStorage();
                $this->_checkoutSession->clearQuote();
                $this->_checkoutSession->clearStorage();
                $this->_checkoutSession->clearHelperData();
                $this->_checkoutSession->resetCheckout();
                $this->_checkoutSession->restoreQuote();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteFactory =$objectManager->create('\Magento\Quote\Model\QuoteFactory');
        $currentQuoteObj = $quoteFactory->create()->load($quote_id);
        $currentQuoteObj->setIsActive(false)->save();
        $currentQuoteObj->setIsActive(true)->save();*/
        try{
                $id = $quote_id;
                // add another condition !!!!!
                //check the item in cart 
                //if yes check the quote id
                //if same as this //check the backend
                    //if allow dont add otherwise add
                //if different than 
                    //than allow user to remove cart
               
                // if($helper->isUserRestict() && $helper->getQuoteIdforSharing()!="" && $helper->getQuoteIdforSharing()==$id )
                // {
                //     $q_id=$helper->getQuoteIdforSharing();
                    
                    
                // }else{

                // }
                
                
                if($id > 0)
                {
                    $quote = $this->quoteFactory->create()->load($id);
                    $items = $quote->getAllVisibleItems();
                    
                    foreach ($items as $item)
                    {
                        $productId =$item->getProductId();
                        $_product = $this->product->create()->load($productId); 
                        $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                        $info = $options['info_buyRequest'];
                                    $request1 = new \Magento\Framework\DataObject();
                                    $request1->setData($info);
                                    
                                    $this->cart->addProduct($_product, $request1);
                                    
                    }
                    $this->cart->save();   
                }
                
            }
            catch (\Exception $e)
            {
                $this->messageManager->addError( __($e->getMessage()) );
            }
           	 






                               $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                               // Your code
            $resultRedirect->setPath('checkout/cart/');
                               return $resultRedirect;

    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

    /**
     * @param array $post Post data from contact form
     * @return void
     */
    private function sendEmail($post)
    {
        $this->mail->send($post['email'], ['data' => new \Magento\Framework\DataObject($post)]);
    }

    /**
     * @return bool
     */
    private function isPostRequest()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        return !empty($request->getPostValue());
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('name')) === '') {
            throw new LocalizedException(__('Name is missing'));
        }
        if (trim($request->getParam('comment')) === '') {
            throw new LocalizedException(__('Comment is missing'));
        }
        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
        if (trim($request->getParam('hideit')) !== '') {
            throw new \Exception();
        }

        return $request->getParams();
    }
}
