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

class Post extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;
    protected $scopeConfig;
    protected $_transportBuilder;
    protected $storeManager;

 
    const XML_PATH_EMAIL_SENDER = 'sharecart/email/sender';
    const XML_PATH_EMAIL_SUBJECT = 'sharecart/email/subject';
    

        public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        \Magento\Checkout\Model\Cart $cart,
        ProductFactory $product,
        QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
             $this->_transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->jsonFactory = $jsonFactory;
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
                           //	$this->cart = $cart;
   	        $this->product = $product;
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig; 
        parent::__construct($context); 
    }

    public function typeofproduct($pid)
    {
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($pid);
        return $product->getTypeId();
    }
    public function getpid($dataarray, $pid)
    {
        

    }
    public function getchildproduct($pid)
    {
        $simarray=array();
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $itemsCollection = $cart->getQuote()->getItemsCollection();
        $myyarr=array();
        $myyarr1=array();
        foreach($itemsCollection as $item) {
        ;
            if($item->getItemId()==$pid )//&& $item->getParentItemId()!=null)
            {
                $myyarr1= array('pid' => $item->getProductId(),
                'quantity' => $item->getQty(),
                );
               
               // echo $item->getProductId();
               // $myyarr[]=$myyarr1;
            }   
            
        }
       // print_r($myyarr1['pid']);
        return $myyarr1;

    }
    public function getConfigurableParentIds()
    {
        $simarray=array();
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $itemsCollection = $cart->getQuote()->getItemsCollection();
        $myyarr=array();
        foreach($itemsCollection as $item) {
            $myyarr1=array();
            if($item->getProductType()!="configurable" && $item->getParentItemId()!=null)
            {
                $childarr=$this->getchildproduct($item->getParentItemId());
               // echo "Hello";
               // print_r($this->getchildproduct($item->getParentItemId()));
                $myyarr1= array('parent_pid' => $item->getProductId(),
                'child_id' => $item->getParentItemId(),
                'item_id' => $childarr['pid'],
                'qtn' => $childarr['quantity'],
                );
               
                
               // echo $item->getProductId();
                $myyarr[]=$myyarr1;
            }   
            
        }
        //print_r($myyarr);
        return $myyarr;

    }
    public function getSimpleProductIds()
    {
        $simarray=array();
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $itemsCollection = $cart->getQuote()->getItemsCollection();
        $myyarr=array();
        foreach($itemsCollection as $item) {
            $myyarr1=array();
            if($item->getProductType()!="configurable" && $item->getParentItemId()==null)
            {

                $myyarr1= array('pid' => $item->getProductId(),
                'quantity' => $item->getQty(),
                );
               
               // echo $item->getProductId();
                $myyarr[]=$myyarr1;
            }   
            
        }
      //  print_r($myyarr);
        return $myyarr;
    }
    public function getParentOfConfig()
    {
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $itemsVisible = $cart->getQuote()->getAllVisibleItems();
        $items = $cart->getQuote()->getAllItems();
 
       // $parentproduct=array();
        //array_merge(array_diff($items, $itemsVisible), array_diff($itemsVisible, $items));
        $parentproduct=  array_diff( $items,$itemsVisible);
    }
    public function getCartProductUrl(){


       // base64_encode($text);
        print_r($this->getConfigurableParentIds());

        $link="";
        if($this->getSimpleProductIds())
        {
            print_r($this->getSimpleProductIds());
            foreach($this->getSimpleProductIds() as $item) {
            
            
            }
        }

    }
    public function getQuoteIdforSharing()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
        $id=0;
        $itemsCollection = $cart->getQuote()->getItemsCollection();
        if($itemsCollection->getData())
        {
            $id=$itemsCollection->getData()[0]['quote_id'];
        }
        return $id;
    }
    public function cuslogin()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if($customerSession->isLoggedIn()) {
        return 1;
        }
        return 0;

    }
    public function execute()
    {

        /*$quote_id=$this->getQuoteIdforSharing();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $url=$storeManager->getStore()->getBaseUrl();
        $url=$url."sharecart/index/run?quote_id=".$quote_id;*/

        $helper = $this->_objectManager->create('FME\ShareCart\Helper\Data');
          // print_r($helper->getUrlforSharing());     
        $link=$helper->getUrlforSharing();

     //   exit;
        $resultJson = $this->jsonFactory->create();
        $post = (array) $this->getRequest()->getPost();
        $error = false;
        $message = '';   
       $post1['customer_id']= 23;
       $post1['quote_id']= 7;
       $post1['sharing_method']= 1;
       $post1['share_from']=$post['youemailaddress'];
       $post1['share_to']=$post['repemailaddress'];
       $post1['message']=$post['msg'];
       $post1['grand_total']= "20";//Need to WOrk
    

      /*$post1['customer_id']= 23;
      $post1['quote_id']= 7;
      $post1['sharing_method']= 1;
      $post1['share_from']="atta.rehman@unitedsol.net";
      $post1['share_to']="attaurrahman092@gmail.com";
      $post1['message']="My Name is Khan";
      $post1['grand_total']= "20";//Need t
        */
        //$link="https://twitter.com/messages/compose?recipient_id=3805104374&text=Hello%20world";
        try {
            //if ((!empty($post)) && (! $error)) {
                if (true) {
                $model = $this->_objectManager->create('FME\ShareCart\Model\Sharecart');
               // $model->setData($post1);
                

                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $subject=$this->scopeConfig->getValue(self::XML_PATH_EMAIL_SUBJECT, $storeScope);
               
               $newData = ['subject' => $subject,'sender_name' => $post['name'], 'sharing_link' => $link, 'message' => $post['msg'] ,'friend_email' =>$post1['share_from']];
            //   $newData = ['subject' => 'Share Cart','sender_name' => 'Atta', 'sharing_link' => 'link', 'message' => 'message'];
               $model->save();
              
                //working
                $response_msg = "Shared Done";// $this->scopeConfig->getValue(self::XML_RESPONSE_MSG);
                /*Email to Sending Start*/
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($newData);
                 
               // print_r($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT));exit;
           $transport = $this->_transportBuilder
                                ->setTemplateIdentifier("Sharecart_email_template")
                                ->setTemplateOptions(
                                    [
                                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                        'store' => $this->storeManager->getStore()->getId(),
                                    ]
                                )
                                ->setTemplateVars(['data' => $postObject])
                                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                                ->addTo($post1['share_to'])
                                ->setReplyTo($post1['share_from'])
                                ->getTransport();
                            
                
                $transport->sendMessage();
                
        
            $result = $this->jsonFactory->create();
            $result->setData(['output' => $response_msg,'error' => 'false']);
               
            
                
            return $result;
            }
        } catch (\Exception $ex) {
                $message = $ex->getMessage();
                $error = true;
                $resultJson->setData(['output' => $message,'error' => 'true']);
               
                return $resultJson;
        }
            $resultJson->setData(['output' => "Something Went Wrong", 'error' => 'true']);
            
            return $resultJson;
       
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
