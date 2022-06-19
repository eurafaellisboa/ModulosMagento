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

class Addtocart extends \Magento\Framework\App\Action\Action
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
        \Magento\Checkout\Model\Session $checkoutSession,
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

        $this->_checkoutSession = $checkoutSession;
        $this->quoteFactory = $quoteFactory;
        $this->request = $request;
                           //	$this->cart = $cart;
   	        $this->product = $product;
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

   
    public function execute()
    {

        $checkeditem = (array) $this->getRequest()->getPost("ischeck");
       
        if(count($checkeditem))
        {

            foreach($checkeditem as $ids) {
                try{

                        $id = $ids;
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
