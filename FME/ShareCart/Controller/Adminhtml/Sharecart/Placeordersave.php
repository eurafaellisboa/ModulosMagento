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
namespace FME\ShareCart\Controller\Adminhtml\Sharecart;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use FME\ShareCart\Model\Sharecart;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Inspection\Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Placeorder extends \Magento\Backend\App\Action
{
    protected $dataPersistor;
    protected $scopeConfig;
    protected $_escaper;
    protected $inlineTranslation;
    protected $_dateFactory;
    protected $uploaderPool;

    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->scopeConfig = $scopeConfig;
        $this->_escaper = $escaper;
        $this->_dateFactory = $dateFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->orderSender = $orderSender;
        parent::__construct($context);
    }

    public function execute()
    {



        $orderInfo =[
            'currency_id'  => 'USD',
            'email'        => 'rakesh.jesadiya@testttttttt.com', //customer email id
            'address' =>[
                'firstname'    => 'Rakesh',
                'lastname'     => 'Testname',
                'prefix' => '',
                'suffix' => '',
                'street' => 'B1 Abcd street',
                'city' => 'Los Angeles',
                'country_id' => 'US',
                'region' => 'California',
                'region_id' => '12', // State region id
                'postcode' => '45454',
                'telephone' => '1234512345',
                'fax' => '12345',
                'save_in_address_book' => 1
            ],
            'items'=>
                [
                    ['product_id'=>'1','qty'=>1], //simple product
                    ['product_id'=>'67','qty'=>2,'super_attribute' => array(93=>52,142=>167)] //configurable product, pass super_attribte for configurable product
                ]
        ];




        $store = $this->storeManager->getStore();
        $storeId = $store->getStoreId();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        //$customer = $this->customerFactory->create();
        $customer= $this->customerRepository->getById(1);
        $customer->setWebsiteId($websiteId);
       // $customer->loadByEmail($orderInfo['email']);// load customet by email address
        //$customer= $this->customerRepository->getById(1);
        if(!$customer->getId()){
            //For guest customer create new cusotmer
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderInfo['address']['firstname'])
                    ->setLastname($orderInfo['address']['lastname'])
                    ->setEmail($orderInfo['email'])
                    ->setPassword($orderInfo['email']);
            $customer->save();
        }

      //  $quote=$this->quote->create(); //Create object of quote
        $quote = $this->quote->create()->load(45);

        $quote->setStore($store); //set store for our quote
        /* for registered customer */
        //$customer= $this->customerRepository->getById($customer->getId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
        $quote->getBillingAddress()->addData($orderInfo['address']);
        $quote->getShippingAddress()->addData($orderInfo['address']);

        // set shipping method
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('flatrate_flatrate'); //shipping method, please verify flat rate shipping must be enable
        $quote->setPaymentMethod('checkmo'); //payment method, please verify checkmo must be enable from admin
        $quote->setInventoryProcessed(false); //decrease item stock equal to qty
        $quote->save(); //quote save 
        // Set Sales Order Payment, We have taken check/money order
        $quote->getPayment()->importData(['method' => 'checkmo']);
 
        // Collect Quote Totals & Save
        $quote->collectTotals()->save();
        // Create Order From Quote Object
        $order = $this->quoteManagement->submit($quote);
        /* for send order email to customer email id */
        $this->orderSender->send($order);
        /* get order real id from order */
        $orderId = $order->getIncrementId();
        if($orderId){
            $result['success']= $orderId;
        }else{
            $result=['error'=>true,'msg'=>'Error occurs for Order placed'];
        }
        return $result;

    }

    protected function getUploader($type)
    {
        return $this->uploaderPool->getUploader($type);
    }
}
