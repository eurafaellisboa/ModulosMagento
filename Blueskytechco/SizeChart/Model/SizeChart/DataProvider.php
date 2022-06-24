<?php
namespace Blueskytechco\SizeChart\Model\SizeChart;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Blueskytechco\SizeChart\Model\ResourceModel\SizeChart\CollectionFactory;

class DataProvider extends UiDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $patternCollectionFactory;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CollectionFactory $patternCollectionFactory
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $patternCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->patternCollectionFactory = $patternCollectionFactory;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) { 
            return $this->loadedData;
        }

        $items = $this->patternCollectionFactory->create()->getItems();
        $this->loadedData = [];
        foreach ($items as $pattern) {
            $data = $pattern->getData();
            $data['category_ids'] = explode(',', $data['category_ids']);
            $this->loadedData[$pattern->getId()] = $data;
        }

        return $this->loadedData;
    }
}
