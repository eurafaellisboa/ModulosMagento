<?php

namespace MGS\Guestwishlist\Controller\Index;


use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param ProductRepositoryInterface $productRepository
     * @param \MGS\Guestwishlist\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @codeCoverageIgnore
     */
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \MGS\Guestwishlist\Helper\Data $helper, \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->_helper = $helper;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/');
        }
        
        $requestParams = $this->getRequest()->getParams();
        
        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        if (!$productId) {
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }
        
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
            $resultRedirect->setPath('*/');
            return $resultRedirect;
        }

        try {
            $itemId = $this->_helper->getRandomString();
            $item = [
                'item_id' => $itemId,
                'qty' => 1,
                'info_buyRequest' => $requestParams
            ];
            if ($product->getTypeId() === 'configurable' && isset($requestParams['super_attribute']) && is_array($requestParams['super_attribute'])) {
                $item['super_attribute'] = $requestParams['super_attribute'];
            }
            $cookie = $this->_helper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME);
            /*
             * check existing product with same options
             * if yes, we don't need add to wishlist
             */
            if (!$this->_helper->checkExistItem($productId, $item, $cookie)) {
                $cookie[$productId][$itemId] = $item;
                $metadata = $this->_cookieMetadataFactory
                        ->createPublicCookieMetadata()
                        ->setPath('/')
                        ->setDuration(86400);
                $this->_cookieManager->setPublicCookie(
                        \MGS\Guestwishlist\Helper\Data::COOKIE_NAME, serialize($cookie), $metadata
                );
            }
            $this->messageManager->addComplexSuccessMessage(
                'addProductSuccessMessage',
                [
                    'product_name' => $product->getName(),
                    'referer' => $product->getProductLinks()
                ]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                    __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addException(
                    $e, __('We can\'t add the item to Wish List right now.')
            );
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        $resultRedirect->setPath('*');
        return $resultRedirect;
    }

}
