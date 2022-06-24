<?php
/**
 * Class Result
 *
 * PHP version 7
 *
 * @category Sparsh
 * @package  Sparsh_AjaxLayeredNavigation
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\AjaxLayeredNavigation\Plugin\CatalogSearch;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Result
 *
 * @category Sparsh
 * @package  Sparsh_AjaxLayeredNavigation
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Result
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactoryCreated;

    /**
     * @var Data
     */
    protected $catalogSearchHelperData;

    /**
     * Result constructor.
     *
     * @param Data $catalogSearchHelperData
     * @param StoreManagerInterface  $storeManager
     * @param QueryFactory           $queryFactory
     * @param Resolver               $layerResolver
     * @param JsonFactory            $resultJsonFactory
     * @param PageFactory            $resultPageFactory
     */
    public function __construct(
        Data $catalogSearchHelperData,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        JsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory

    ) {
        $this->storeManager = $storeManager;
        $this->queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultPageFactoryCreated = $resultPageFactory->create();
        $this->catalogSearchHelperData = $catalogSearchHelperData;
    }


    /**
     * @param \Magento\CatalogSearch\Controller\Result\Index $subject
     * @param \Closure                                       $method
     * @return \Magento\Framework\Controller\Result\Json|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundExecute(
        \Magento\CatalogSearch\Controller\Result\Index $subject,
        \Closure $method
    ) {
        if ($subject->getRequest()->getParam('ajax') == 1) {
            $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);

            $query = $this->queryFactory->get();

            $query->setStoreId($this->storeManager->getStore()->getId());

            $resultJson = $this->resultJsonFactory->create();

            if ($query->getQueryText() != '') {
                if ($this->catalogSearchHelperData->isMinQueryLength()) {
                    $query->setId(0)->setIsActive(1)->setIsProcessed(1);
                } else {
                    $query->saveIncrementalPopularity();

                    if ($query->getRedirect()) {
                        $data = [
                            'success' => true,
                            'redirect_url' => $query->getRedirect()
                        ];
                        return $resultJson->setData($data);
                    }
                }

                $this->catalogSearchHelperData->checkNotes();
                $resultsBlockHtml = $this->resultPageFactoryCreated->getLayout()->getBlock('search.result')
                    ->toHtml();
                $leftNavBlockHtml = $this->resultPageFactoryCreated->getLayout()->getBlock('catalogsearch.leftnav')
                    ->toHtml();

                return $this->resultJsonFactory->create()->setData(
                    ['success' => true, 'html' => [
                    'products_list' => $resultsBlockHtml,
                    'filters' => $leftNavBlockHtml
                    ]]
                );
            } else {
                $data = [
                    'success' => true,
                    'redirect_url' => $this->_redirect->getRedirectUrl()
                ];
                return $resultJson->setData($data);
            }
        } else {
            return $method();
        }
    }
}
