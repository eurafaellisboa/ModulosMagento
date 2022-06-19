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
namespace FME\ShareCart\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Customer $customermodel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->coreSession = $coreSession;
        $this->_objectManager = $objectManager;
        $this->_currentStoreView = $this->_storeManager->getStore();
        $this->customermodel=$customermodel;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    
    }
    public function setSessionforquoteid($val){
        $this->coreSession->start();
        $this->coreSession->setTestquoteid($val);
    }
    public function setSessionforcartname($val){
        $this->coreSession->start();
        $this->coreSession->setTestcartname($val);
    }
//forcartname
    public function unSetSessionforcartname(){
        $this->coreSession->start();
        return $this->coreSession->unsTestcartname();
    }
    public function unSetSessionforquoteid(){
        $this->coreSession->start();
        return $this->coreSession->unsTestquoteid();
    }
    public function getSessionforcartname(){
            
        $this->coreSession->start();
        return $this->coreSession->getTestcartname();
    }
    public function getSessionforquoteid(){
        
        $this->coreSession->start();
        return $this->coreSession->getTestquoteid();
    }
    function my_simple_crypt( $string, $action = 'e' ) {
        // you may change these values to your own
        $secret_key = 'my_simple_secret_key';
        $secret_iv = 'my_simple_secret_iv';
     
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
     
        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }
     
        return $output;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        if($this->getSessionforquoteid()!="")
        {
            $customer = $observer->getEvent()->getCustomer();
            $cust_id=$customer->getData("entity_id");
            $quote_id =$this->getSessionforquoteid();
        
            $quote_id=$this->my_simple_crypt($quote_id,"e");
        
            $url=$this->_currentStoreView->getBaseUrl();
            $url=$url."sharecart/index/run?quote_id=".$quote_id;

            $post1['cartname']=$this->getSessionforcartname();
            $post1['customer_id']= $cust_id;
            $post1['quote_id']= $this->getSessionforquoteid();
            $post1['sharing_method']= 1;
            $post1['share_from']="";
            $post1['share_to']="";
            $post1['message']=$url;//can use for link
            $post1['grand_total']= "0";//Need to WOrk
            $model = $this->_objectManager->create('FME\ShareCart\Model\Sharecart');
            $model->setData($post1);
            $model->save();
            $this->unSetSessionforquoteid();
            $this->unSetSessionforcartname();
        }
        
    }
}