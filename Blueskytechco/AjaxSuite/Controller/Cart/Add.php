<?php
namespace Blueskytechco\AjaxSuite\Controller\Cart;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Setup\Exception;
use Magento\Framework\View\LayoutFactory;

/**
 * Controller for processing add to cart action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Registry $registry,
        LayoutFactory $layoutFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $productRepository
        );
        $this->productRepository = $productRepository;
        $this->jsonHelper = $jsonHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->viewHelper = $viewHelper;
        $this->layoutFactory = $layoutFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $results = ['error' => '', 'success' => '', 'url' => '', 'content' => '', 'minicart' => '', 'add_cart_success' => ''];
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $results['error'] = __('Your session has expired');
            return $this->jsonResponse($results);
        }

        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get(
                        \Magento\Framework\Locale\ResolverInterface::class
                    )->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            $productId = (int)$this->getRequest()->getParam('product');
            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $results['error'] = __('No product added.');
                return $this->jsonResponse($results);
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );
            $success = '';
            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $success = __(
                        'You added %1 to your shopping cart.',
                        $product->getName() . $product->getFinalPrice()
                    );
                    $this->messageManager->addSuccessMessage($success);
                }
            }
            $items = [];
            $prIds = [$product->getId() => $product->getId()];
            if($groupItems = $this->getRequest()->getParam('super_group'))
            {
                foreach($groupItems as $id => $qty)
                {
                    $productGr = $this->_getProductById($id);
                    if($qty > 0 && $productGr)
                    {
                        $prIds[$productGr->getId()] = $productGr->getId();
                    }
                }
            }elseif ($product->getTypeId() ===
                \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE)
            {
                $childProductCollection = $product->getTypeInstance()->getAssociatedProducts($product);
                foreach($childProductCollection as $pr)
                {
                    $prIds[$pr->getId()] = $pr->getId();
                }
            }
            if($prId = $this->cart->getCheckoutSession()->getLastAddedProductId())
            {
                $prIds[$prId] = $prId;
            }

            foreach($this->cart->getQuote()->getAllItems() as $item)
            {
                if($item->getProduct() && in_array($item->getProduct()->getId(), $prIds))
                {
                    $items[] = $item;
                }
            }
            $this->_coreRegistry->register('quote_item', $items);
            $this->_coreRegistry->register('product', $product);
            $this->_coreRegistry->register('current_product', $product);

            $layout = $this->layoutFactory->create();
            $update = $layout->getUpdate();
            $update->load('ajaxsuite_addcart_success');
            $layout->generateXml();
            $layout->generateElements();
            $results['success'] = $layout->getOutput();
            $results['add_cart_success'] = 'success';
            return $this->jsonResponse($results);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($message)
                    );
                }
            }
            $results['error'] = $e->getMessage();

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $url = $this->_redirect->getRedirectUrl($this->getCartUrl());
            }
            $results['url'] = $url;
            return $this->jsonResponse($results);
        } catch (\Exception $e) {
            $results['error'] = __('We can\'t add this item to your shopping cart right now.');
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        return $this->jsonResponse($results);
    }
    protected function _getProductById($productId)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
    /**
     * Returns cart url
     *
     * @return string
     */
    private function getCartUrl()
    {
        return $this->_url->getUrl('checkout/cart', ['_secure' => true]);
    }

    /**
     * Is redirect should be performed after the product was added to cart.
     *
     * @return bool
     */
    private function shouldRedirectToCart()
    {
        return $this->_scopeConfig->isSetFlag(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
