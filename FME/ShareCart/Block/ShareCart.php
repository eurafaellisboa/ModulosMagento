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
namespace FME\ShareCart\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use FME\ShareCart\Helper\Data as DataHelper;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\App\RequestInterface;


/**
 * Class RelatedChart
 * @package FME\SizeChart\Block\Post
 */
class ShareCart extends Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

     /**
     * objectManager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

         /**
     * _sizechartHelper
     *
     * @var \FME\SizeChart\Helper\Data
     */
    protected $_sizechartHelper;

    /**
     * RelatedChart constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DataHelper $_helperGallery,
        RequestInterface $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        QuoteFactory $quoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Request\Http $httprequest,
        ObjectManagerInterface $objectManager,         
        array $data = []
    )
    {
        $this->_helperGallery=$_helperGallery;   
        $this->_coreRegistry = $registry;
        $this->request = $request;
        $this->_session = $session;
        $this->_storeManager = $storeManager;
        $this->_currentStoreView = $this->_storeManager->getStore();
        $this->httprequest = $httprequest;
        $this->objectManager = $objectManager;
        $this->_checkoutSession=$checkoutSession;
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context, $data);
        $this->setTabTitle();
    }
    

    public function getMyPostParams()
    {
        $this->request->getParams(); // all params
        $quote_id=$this->request->getParam('quote_id');
        if(strlen($quote_id)>0)
        {
            $quote_id=$this->_helperGallery->my_simple_crypt($quote_id,"d");
        }
        return $quote_id;
    }
    public function getFormUrl()
    {
       // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $url= $this->_currentStoreView->getBaseUrl();
        $url=$url."sharecart/index/updatecart";
        return $url;
        

    }
    public function sessionBreak($quoteid)
    {
       // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quoteFactory =$this->quoteFactory;
        $currentQuoteObj = $quoteFactory->create()->load($quoteid);
        $currentQuoteObj->setIsActive(false)->save();
                $this->_checkoutSession->clearQuote()->clearStorage();
                $this->_checkoutSession->clearQuote();
                $this->_checkoutSession->clearStorage();
                $this->_checkoutSession->clearHelperData();
                $this->_checkoutSession->resetCheckout();
                $this->_checkoutSession->restoreQuote();
    }
    public function getCartdetailById($quote_id)
    {
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $items = $quote->getAllVisibleItems();
        return  $items;
    }
    public function getSubTotalById($quote_id)
    {
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $items = $quote->getSubtotal();
        return  $items;
    }
    public function getGrandTotalById($quote_id)
    {
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $items = $quote->getGrandTotal();
        return  $items;
    }
    public function getCartItems($quote_id)
    {
        // if(strlen($quote_id)>0)
        // {
        //     $quote_id=$this->_helperGallery->my_simple_crypt($quote_id,"d");
        // }
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $items = $quote->getAllVisibleItems();
        return  $items;
    }
    public function getVariables()
    {
        $this->request->getParams(); // all params
        $quote_id=$this->request->getParam('quote_id');

        //print_r( $quote_id);exit;
        //$this->quoteRepository->get($quote_id);
        /*$this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/cart/');
        return $resultRedirect;*/
        //$helper = $this->_objectManager->create('FME\ShareCart\Helper\Data');
        if(strlen($quote_id)>0)
        {
            $quote_id=$this->_helperGallery->my_simple_crypt($quote_id,"d");
        }
        $quote = $this->quoteFactory->create()->loadByIdWithoutStore($quote_id);
        $items = $quote->getAllVisibleItems();
        return  $items;
    }
}
