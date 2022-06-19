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
namespace FME\ShareCart\Helper;

use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session;
use FME\ShareCart\Model\SharecartFactory;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PRODUCTZOOM_ENABLE                    ='sharecart/general/enableModule';
    const XML_SHARECART_STORE                   ='sharecart/store_enable/store';
    const XML_PRODUCTZOOM_RESTICT                  ='sharecart/general/restictuser';
    const XML_SHARECART_COPYLINKENABLE                    ='sharecart/general/showandhide';
    const XML_SHARECART_GRIDHEAD                 ='sharecart/general/sctext';
   
    //save button option
    const XML_SHAREBTNCART_BACKCOLOR                   ='sharecart/sharecartbtn/backcolor';
    const XML_SHAREBTNCART_TEXTCOLOR                   ='sharecart/sharecartbtn/textcolcolor';
    const XML_SHAREBTNCART_TEXT                   ='sharecart/sharecartbtn/stext';
    const XML_SAVEBTNCART_TEXT                   ='sharecart/sharecartbtn/svtext';
    const XML_SHAREBTNCART_POS                  ='sharecart/sharecartbtn/btnposition';
   
    //email
    const XML_SHAREEMAIL_EN                  ='sharecart/emailbtn/enablebtn';
    const XML_SHAREEMAIL_TXT                 ='sharecart/emailbtn/btntxt';
    const XML_SHAREEMAIL_IMG                  ='sharecart/emailbtn/upload_image_id';
    //fb
    const XML_SHAREFB_EN                  ='sharecart/fbbtn/enablebtn';
    const XML_SHAREFB_TXT                 ='sharecart/fbbtn/btntxt';
    const XML_SHAREFB_Text                ='sharecart/fbbtn/txt';
    const XML_SHAREFB_IMG                  ='sharecart/fbbtn/upload_image_id';
    //whatsapp
    const XML_SHAREWHATSAPP_EN                  ='sharecart/whatbtn/enablebtn';
    const XML_SHAREWHATSAPP_TXT                 ='sharecart/whatbtn/btntxt';
    const XML_SHAREWHATSAPP_Text                ='sharecart/whatbtn/txt';
    const XML_SHAREWHATSAPP_IMG                  ='sharecart/whatbtn/upload_image_id';
     //Twitter
     const XML_SHARETWITTER_EN                  ='sharecart/twitterbtn/enablebtn';
     const XML_SHARETWITTER_TXT                 ='sharecart/twitterbtn/btntxt';
     const XML_SHARETWITTER_Text                ='sharecart/twitterbtn/txt';
     const XML_SHARETWITTER_IMG                  ='sharecart/twitterbtn/upload_image_id';
     
     //Linkedin
     const XML_SHARELINKEDIN_EN                  ='sharecart/linkedinbtn/enablebtn';
     const XML_SHARELINKEDIN_TXT                 ='sharecart/linkedinbtn/btntxt';
     const XML_SHARELINKEDIN_Text                ='sharecart/linkedinbtn/txt';
     const XML_SHARELINKEDIN_IMG                  ='sharecart/linkedinbtn/upload_image_id';
     
    //Linkedin
    const XML_SHAREPONTEREST_EN                  ='sharecart/pinterestbtn/enablebtn';
    const XML_SHAREPONTEREST_TXT                 ='sharecart/pinterestbtn/btntxt';
    const XML_SHAREPONTEREST_Text                ='sharecart/pinterestbtn/txt';
    const XML_SHAREPONTEREST_IMG                  ='sharecart/pinterestbtn/upload_image_id';
    
    //Reddit
    const XML_SHAREREDDIT_EN                  ='sharecart/redditbtn/enablebtn';
    const XML_SHAREREDDIT_TXT                 ='sharecart/redditbtn/btntxt';
    const XML_SHAREREDDIT_Text                ='sharecart/redditbtn/txt';
    const XML_SHAREREDDIT_IMG                  ='sharecart/redditbtn/upload_image_id';
    
//VK
const XML_SHAREVK_EN                  ='sharecart/vkbtn/enablebtn';
const XML_SHAREVK_TXT                 ='sharecart/vkbtn/btntxt';
const XML_SHAREVK_Text                ='sharecart/vkbtn/txt';
const XML_SHAREVK_IMG                  ='sharecart/vkbtn/upload_image_id';

//googleplus
const XML_SHAREGPLUS_EN                  ='sharecart/googleplus/enablebtn';
const XML_SHAREGPLUS_TXT                 ='sharecart/googleplus/btntxt';
const XML_SHAREGPLUS_Text                ='sharecart/googleplus/txt';
const XML_SHAREGPLUS_IMG                  ='sharecart/googleplus/upload_image_id';


const XML_SHARE_PDF_COMP_NAME                 ='sharecart/bussinessdetail/compname';
const XML_SHARE_PDF_ADDR                 ='sharecart/bussinessdetail/addressdetail';
const XML_SHARE_PDF_VAT                 ='sharecart/bussinessdetail/vatdetail';
const XML_SHARE_PDF_PHONE                ='sharecart/bussinessdetail/phonedetail';
const XML_SHARE_PDF_EMAIL                ='sharecart/bussinessdetail/emaildetail';
const XML_SHARE_PDF_REG               ='sharecart/bussinessdetail/regdetail';
const XML_SHARE_PDF_WARN_MSG               ='sharecart/bussinessdetail/warndetail';




    /*
   $image = $objectManager->create('FME\Testimonials\Model\Config\Backend\Image');
 $url=\FME\Testimonials\Model\Config\Backend\Image::UPLOAD_DIR.'/'.$helper->getDefaultImageUrl();
$imageUrl=$block->getMediaUrl().$url;
   */


    protected $_ruleFactory;
    protected $_currentStoreView;
    protected $_coreRegistry;
    protected $_customerSession;
 
    protected $_storeManager;
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Session $customerSession,
        \Magento\Framework\Registry $coreRegistry,
        SharecartFactory $collectionFactory,
        \FME\ShareCart\Model\Sharecart $shareCartModel,
        \Magento\Checkout\Model\Cart $cartmodel,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->_currentStoreView = $this->_storeManager->getStore();
        $this->_coreRegistry = $coreRegistry;
        $this->cartmodel=$cartmodel;
        $this->shareCartModel=$shareCartModel;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    //XML_SHARECART_STORE
    public function getStoreIds()
    {
        return $this->scopeConfig->getValue(self::XML_SHARECART_STORE);
    }
    public function getPdfWarnMsg()
    {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_WARN_MSG);
    }
    public function getPdfReg()
    {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_REG);
    }
    public function getPdfEmail()
     {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_EMAIL);
    }
    public function getPdfPhone()
    {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_PHONE);
    }
    public function getPdfVAT()
    {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_VAT);
    }
 
    public function getPdfAddress()
    {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_ADDR); 
    }
 
    public function getPdfCompanyName()
    {
        return $this->scopeConfig->getValue(self::XML_SHARE_PDF_COMP_NAME);
    }
    public function getCartData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $itemsCollection = $cart->getQuote()->getItemsCollection();
        $itemsVisible = $cart->getQuote()->getAllVisibleItems();
        $items = $this->cartmodel->getQuote()->getAllVisibleItems();
        return $items;
    }
    public function getMyCartUrl()
    {

        $url= $this->_currentStoreView->getBaseUrl();
        $url=$url."sharecart/index/mysavecart/";
        return $url;
    }
    public function get_tiny_url($url)  {  
        $ch = curl_init();  
        $timeout = 5;  
        curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
        $data = curl_exec($ch);  
        curl_close($ch);  
        return $data;  
    }
    public function getShareCartData()
    {
        $cusid=$this->_customerSession->getCustomerId();
        $resultPage = $this->collectionFactory->create();
        $collection = $resultPage->getCollection(); //Get Collection of module data
        $collection=$collection->addFieldToFilter('customer_id', $cusid);
        // print_r($collection->getData());
        //exit;
        return $collection;
    }
    public function getShareCartLabel($quote_id)
    {
        $cusid=$this->_customerSession->getCustomerId();
        $resultPage = $this->collectionFactory->create();
        $collection = $resultPage->getCollection(); //Get Collection of module data
        $collection=$collection->addFieldToFilter('customer_id', $cusid);
        $collection=$collection->addFieldToFilter('quote_id', $quote_id);
      //  echo $collection->getSelect();
       // print_r($collection->getData());
       // exit;
       $label="";
       if(count($collection)>0)
       {
        $label=$collection->getData()[0]['cartname'];
       }
        return  $label;
    }
    //googleplus
    public function getGPLUSBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREGPLUS_IMG);
    }
    public function getGPLUSText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREGPLUS_Text);
    }
    public function getGPLUSBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREGPLUS_TXT);
    }
    public function getGPLUSBtnEnable()
    {
        return false;
        // $isEnabled = true;
        // $enabled = $this->scopeConfig->getValue(self::XML_SHAREGPLUS_EN, ScopeInterface::SCOPE_STORE);
        // if ($enabled == null || $enabled == '0') {
        //     $isEnabled = false;
        // }
        // return $isEnabled;
    }
    
    //VK
    public function getsharecarts()
    {
        
    }
    public function getVKBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREVK_IMG);
    }
    public function getVKText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREVK_Text);
    }
    public function getVKBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREVK_TXT);
    }
    public function getVKBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHAREVK_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    //REddit
    public function getREDDITBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREREDDIT_IMG);
    }
    public function getREDDITText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREREDDIT_Text);
    }
    public function getREDDITBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREREDDIT_TXT);
    }
    public function getREDDITBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHAREREDDIT_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    //Pinteresgt
    public function getPONTERESTBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREPONTEREST_IMG);
    }
    public function getPONTERESTText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREPONTEREST_Text);
    }
    public function getPONTERESTBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREPONTEREST_TXT);
    }
    public function getPONTERESTBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHAREPONTEREST_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    //LINKEDIN
    public function getLINKEDINBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHARELINKEDIN_IMG);
    }
    public function getLINKEDINText()
    {
        return $this->scopeConfig->getValue(self::XML_SHARELINKEDIN_Text);
    }
    public function getLINKEDINBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHARELINKEDIN_TXT);
    }
    public function getLINKEDINBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHARELINKEDIN_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    //Twitter
    public function getTWITTERBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHARETWITTER_IMG);
    }
    public function getTWITTERText()
    {
        return $this->scopeConfig->getValue(self::XML_SHARETWITTER_Text);
    }
    public function getTWITTERBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHARETWITTER_TXT);
    }
    public function getHeading()
    {
        return $this->scopeConfig->getValue(self::XML_SHARECART_GRIDHEAD);
    }
    public function getTWITTERBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHARETWITTER_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    //Whatsapp
    public function getWhatsappBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREWHATSAPP_IMG);
    }
    public function getWhatsappText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREWHATSAPP_Text);
    }
    public function getWhatsappBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREWHATSAPP_TXT);
    }
    public function getWhatsappBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHAREWHATSAPP_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    //FB
    public function getfbBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREFB_IMG);
    }
    public function getfbText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREFB_Text);
    }
    public function getfbBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREFB_TXT);
    }
    public function getfbBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHAREFB_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }

    //email options
    //XML_SHAREEMAIL_EN
    public function getEMailBtnEnable()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHAREEMAIL_EN, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    public function getEMailBtnText()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREEMAIL_TXT);
    }
    public function getEMailBtnLogo()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREEMAIL_IMG);
    }
    //share Btn Option
    //XML_SHAREBTNCART_TEXTCOLOR
    public function getBtnTextColor()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREBTNCART_TEXTCOLOR);
    }
    public function getBtnBackColor()
    {
        return $this->scopeConfig->getValue(self::XML_SHAREBTNCART_BACKCOLOR);
    }
    public function getBtnPosition()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREBTNCART_POS);
    }
    //XML_SAVEBTNCART_TEXT
    public function getBtntext()
    {
        
        return $this->scopeConfig->getValue(self::XML_SHAREBTNCART_TEXT);
    }public function getsaveBtntext()
    {
        
        return $this->scopeConfig->getValue(self::XML_SAVEBTNCART_TEXT);
    }
    //XML_PRODUCTZOOM_RESTICT
    public function isUserRestict()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PRODUCTZOOM_RESTICT, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    public function isEnabledInFrontend()
    {
        $store_arr = explode(',', $this->getStoreIds());
        $current_arr=(int)$this->_storeManager->getStore()->getId();
        // print_r($store_arr);
        // if($store_arr[0]==0)
        // {
        //     echo "hello";
        // }
        // exit;
        if(count($store_arr)==0)
        {
            return false;
        }
        if(count($store_arr)==1)
        {
            if(!( $store_arr[0]==0 || $store_arr[0]==$current_arr))
            {
                
                return false;
            }
        }
        if(count($store_arr)>1)
        {
            if(!($store_arr[0]==0 || array_search($current_arr,$store_arr)))
            {
                return false;
            }
        }
        
        // print_r((int)$this->_storeManager->getStore()->getId());
        // print_r($this->getStoreIds());exit;
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_PRODUCTZOOM_ENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    public function isEnabledCopyLink()
    {
        $isEnabled = true;
        $enabled = $this->scopeConfig->getValue(self::XML_SHARECART_COPYLINKENABLE, ScopeInterface::SCOPE_STORE);
        if ($enabled == null || $enabled == '0') {
            $isEnabled = false;
        }
        return $isEnabled;
    }
    public function getCurrentCartItem()
    {
        $this->cartmodel->getQuote()->getAllVisibleItems();
        $quote = $this->cartmodel->getQuote();
        $quoteId = $quote->getId();
        return $quoteId;
    }
    public function getQuoteIdforSharing()
    {
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $this->cartmodel;
        $id=0;
        $itemsCollection = $cart->getQuote()->getItemsCollection();
        if($itemsCollection->getData())
        {
            //echo "asdas";exit;
            $id=$itemsCollection->getData()[0]['quote_id'];
        }
        return $id;
    }
    public function getBaseUrl()
    {

       $url=$this->_currentStoreView->getBaseUrl();
       
        return $url;

    }
    public  function getUrlforEditing($quote_id)
    {
        //echo $quote_id;exit; 
        if($quote_id>0)
        {
            $quote_id=$this->my_simple_crypt($quote_id,"e");
            //$quote_id=$this->my_simple_crypt($quote_id,"d");
            $url=$this->_currentStoreView->getBaseUrl();
            $url=$url."sharecart/index/editmycart?quote_id=".$quote_id;
            return $url;
        }
        return  $this->_currentStoreView->getBaseUrl();
    }

    public  function getDeleteItemCart($quote_id,$productid)
    {
        //echo $quote_id;exit; 
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        if($quote_id>0)
        {
            $quote_id=$this->my_simple_crypt($quote_id,"e");
            //$quote_id=$this->my_simple_crypt($quote_id,"d");
            $url=$this->_currentStoreView->getBaseUrl();
            $url=$url."sharecart/index/deleteitem?quote_id=".$quote_id."&product_id=".$productid;
            return $url;
        }
        return  $this->_currentStoreView->getBaseUrl();
    }

    public  function getUrlforSharing()
    {
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       // $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $quote_id=$this->getQuoteIdforSharing();
        if($quote_id>0)
        {
            $quote_id=$this->my_simple_crypt($quote_id,"e");
            //$quote_id=$this->my_simple_crypt($quote_id,"d");
            $url=$this->_currentStoreView->getBaseUrl();
            $url=$url."sharecart/index/run?quote_id=".$quote_id;
            return $url;
        }
        return  $this->_currentStoreView->getBaseUrl();
    }
    public function fbfunction()
    {
        $fbtitle="Check out my Cart";
        //https://www.facebook.com/sharer.php?u=123.abc.com&t=TEst
        $url="https://www.facebook.com/sharer.php?u=".$this->getUrlforSharing()."&t=TEst";
   
        return $url;
    }
    public function whatsappfunction()
    {
        $fbtitle="Veja meu carrinho";
        $url="https://web.whatsapp.com/send?text='".$fbtitle."' \n".$this->getUrlforSharing();
        return $url;
    }
    public function getFormUrl()
    {
       // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $url=$this->_currentStoreView->getBaseUrl();
        $url=$url."sharecart/index/addtocart";
        return $url;
        

    }
    public function twitterfunction()
    {
        $fbtitle="Check out my Cart";
        $url="https://twitter.com/home?status=".$fbtitle."  ".$this->getUrlforSharing();
        return $url;
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
    public function linkedinfunction()
    {
        $fbtitle="Check out my Cart";
        //https://www.linkedin.com/shareArticle?mini=true&url=https://stackoverflow.com

       // $url="https://www.facebook.com/sharer.php?t='".$fbtitle."'&u='".$this->getUrlforSharing();
        $url="https://www.linkedin.com/shareArticle?mini=true&url=".$this->getUrlforSharing();
        return $url;
    }
    function pinterestfunction()
    {
       // https://in.pinterest.com/pin/create/button/?description='+title+'&media=&url='+url
       $fbtitle="Check out my Cart";
       $url="https://in.pinterest.com/pin/create/button/?description=".$fbtitle."&url=".$this->getUrlforSharing();
      // $url="https://www.linkedin.com/shareArticle?mini=true&url=".$this->getUrlforSharing();
       return $url;
    }
    function vKfunction()
    {
       // https://in.pinterest.com/pin/create/button/?description='+title+'&media=&url='+url
       //$fbtitle="Check out my Cart";
       $url="https://vk.com/share.php?url=".$this->getUrlforSharing();
      // $url="https://www.linkedin.com/shareArticle?mini=true&url=".$this->getUrlforSharing();
       return $url;
    }
    function rDfunction()
    {
       // https://in.pinterest.com/pin/create/button/?description='+title+'&media=&url='+url
       $fbtitle="Check out my Cart";
       $url="https://www.reddit.com/submit?url=".$this->getUrlforSharing();
      // $url="https://www.linkedin.com/shareArticle?mini=true&url=".$this->getUrlforSharing();
       return $url;
    }
    
}
