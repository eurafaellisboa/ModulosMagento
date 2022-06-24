<?php
namespace Blueskytechco\SizeChart\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Blueskytechco\SizeChart\Model\SizeChartFactory;
use Blueskytechco\SizeChart\Model\ResourceModel\SizeChart\CollectionFactory;
use Blueskytechco\SizeChart\Api\RepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Controller\Result\JsonFactory;

abstract class AbstractStore extends Action
{
    const ADMIN_RESOURCE = 'Blueskytechco_SizeChart::sizechart';

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Registry
     */
    protected $registry; 

    /**
     * @var Data
     */
    public $jsonHelper;

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var SizeChartFactory
     */
	protected $sizeChartFactory;
	
	 /**
     * @var CollectionFactory
     */
	protected $collectionSizeChartFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
    */
    protected $componentRegistrar;
	
	/**
     * @var \Magento\Framework\UrlInterface
    */
	protected $_urlInterface;
	
	/**
     * @var \Blueskytechco\SizeChart\Api\RepositoryInterface
    */
	protected $sizeChartRepository;
	
    public function __construct(
		Context $context,
		SizeChartFactory $sizeChartFactory,
		CollectionFactory $collectionSizeChartFactory,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        LoggerInterface $logger,
        Data $jsonHelper,
        Registry $registry,
        DateTime $dateTime,
        Filter $filter,
		RepositoryInterface $dataRepository,
        ComponentRegistrar $componentRegistrar,
		\Magento\Framework\UrlInterface $urlInterface,    
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->sizeChartFactory = $sizeChartFactory;
		$this->collectionSizeChartFactory = $collectionSizeChartFactory;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->registry = $registry;
        $this->dateTime = $dateTime;
        $this->filter = $filter;
		$this->sizeChartRepository = $dataRepository;
        $this->componentRegistrar = $componentRegistrar;
        $this->resultJsonFactory = $resultJsonFactory;
		$this->_urlInterface = $urlInterface;
    }
}
