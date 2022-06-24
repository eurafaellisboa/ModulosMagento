<?php
namespace Blueskytechco\SetProduct\Model;

use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Action\Collection;

class ProductSet extends AbstractModel
{
    const BASE_MEDIA_PATH = 'lookbook';
    const CACHE_TAG = 'blueskytechco_setproduct';
    protected $_cacheTag = 'blueskytechco_setproduct';
    protected $_eventPrefix = 'blueskytechco_setproduct'; 

    protected $_request;
    protected $productFactory;
    protected $productCollectionFactory;
    protected $resourceIterator;
	protected $csvProcessor;
	protected $_directoryList;
	
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        Iterator $resourceIterator,
		\Magento\Framework\File\Csv $csvProcessor,
		\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        AbstractResource $resource = null
    ) {
        $this->resourceIterator         = $resourceIterator;
		$this->csvProcessor = $csvProcessor;
		$this->_directoryList = $directoryList;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource);
    }

    protected function _construct()
    {
        $this->_init(\Blueskytechco\SetProduct\Model\ResourceModel\ProductSet::class);
    }

    public function getConditionsInstance()
    {
        return $this->getActionsInstance();
    }
    public function getActionsInstance()
    {
        return ObjectManager::getInstance()->create(Combine::class);
    }
}
