<?php
namespace MGS\AjaxCart\Controller\Cart;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Zend\Uri\UriFactory;

/**
 * Class Add
 *
 * @package MGS\AjaxCart\Controller\Cart
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    private $cartHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Router\PathConfigInterface
     */
    private $pathConfig;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

	/**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
	
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Router\PathConfigInterface $pathConfig
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Router\PathConfigInterface $pathConfig,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Data\Form\FormKey $formKey
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productRepository = $productRepository;
        $this->cartHelper = $cartHelper;
        $this->scopeConfig = $scopeConfig;
        $this->pathConfig = $pathConfig;
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
		$this->formKey = $formKey;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultData = [];
        try {
            $request = $this->getRequest();
            $resultData['backUrl'] = $request->getParam('action_url');
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $resultData['error'] = $e->getMessage();
        }

        return $this->resultJsonFactory->create()
            ->setData($resultData);
    }
}
