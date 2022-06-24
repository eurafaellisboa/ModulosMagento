<?php
declare(strict_types=1);

namespace Blueskytechco\AjaxSuite\Controller\Compare;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\ViewModel\Product\Checker\AddToCompareAvailability;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\LayoutFactory;

class Add extends \Magento\Catalog\Controller\Product\Compare\Add
{
    /**
     * @var AddToCompareAvailability
     */
    private $compareAvailability;
    protected $jsonHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Visitor $customerVisitor
     * @param \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Validator $formKeyValidator
     * @param PageFactory $resultPageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param AddToCompareAvailability|null $compareAvailability
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product\Compare\ItemFactory $compareItemFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Visitor $customerVisitor,
        \Magento\Catalog\Model\Product\Compare\ListCompare $catalogProductCompareList,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        LayoutFactory $layoutFactory,
        AddToCompareAvailability $compareAvailability = null
    ) {
        parent::__construct(
            $context,
            $compareItemFactory,
            $itemCollectionFactory,
            $customerSession,
            $customerVisitor,
            $catalogProductCompareList,
            $catalogSession,
            $storeManager,
            $formKeyValidator,
            $resultPageFactory,
            $productRepository,
            $compareAvailability
        );

        $this->compareAvailability = $compareAvailability
            ?: $this->_objectManager->get(AddToCompareAvailability::class);
        $this->jsonHelper = $jsonHelper;
        $this->_coreRegistry = $registry;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * Add item to compare list.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $requestParams = $this->getRequest()->getParams();
        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
        if (!$productId) {
            return $this->jsonResponse(__('Product not found.'));
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return $this->jsonResponse(__('NoSuchEntityException Product not found.'));
        }

        if (!$product || !$product->isVisibleInCatalog()) {
            return $this->jsonResponse(__('We can\'t specify a product.'));
        }
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }
            $referer = $this->_catalogSession->getBeforeCompareUrl();
            if ($referer) {
                $this->_catalogSession->setBeforeCompareUrl(null);
            } else {
                $referer = $this->_redirect->getRefererUrl();
            }
            if ($product && $this->compareAvailability->isAvailableForCompare($product)) {
                $this->_catalogProductCompareList->addProduct($product);
                $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
                $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();

                $this->_coreRegistry->register('product', $product);
                $this->_coreRegistry->register('current_product', $product);

                $layout = $this->layoutFactory->create();
                $update = $layout->getUpdate();
                $update->load('ajaxsuite_compare_success');
                $layout->generateXml();
                $layout->generateElements();

                $result = [
                    'product_name' => $product->getName(),
                    'html_popup' => $layout->getOutput(),
                    'referer' => $referer
                ];

                return $this->jsonResponse($result);
            }
        }

        return $this->jsonResponse(__('Product not found.'));
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
