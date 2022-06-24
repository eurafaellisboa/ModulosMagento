<?php
namespace Blueskytechco\SetProduct\Controller\Adminhtml\ProductSet;

use Magento\Framework\Controller\ResultFactory;

class ProductsList extends \Magento\Backend\App\Action
{
	protected $_productCollectionFactory;
	protected $_catalogProductVisibility;
	protected $_imageHelper;
	protected $_storeManager;

    public function __construct(
    	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    	\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    	\Magento\Catalog\Helper\Image $imageHelper,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\App\Action\Context $context
    ) {
    	$this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_imageHelper = $imageHelper;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
    	$wholeData = $this->getRequest()->getParams();
    	$pageSize    = 10;
    	$collection = $this->_productCollectionFactory->create();
    	$collection->setStoreId(0);
    	$collection->addFieldToSelect('thumbnail');
    	$collection->addFieldToSelect('name');
    	$collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
    	$queryName  = isset($wholeData["q"]) ? $wholeData["q"] : '';
    	$collection->addAttributeToFilter([
	       ['attribute' => 'name', 'like' => '%'.$queryName.'%'],
	       ['attribute' => 'sku', 'like' => '%'.$queryName.'%']
	    ]);
    	$pageNumber  = isset($wholeData["page"]) ? $wholeData["page"] : 1;
    	$collection->setPageSize($pageSize)->setCurPage($pageNumber);
    	$collection->getSelect()->order("entity_id DESC");
    	$collection->distinct(true);
    	//echo $collection->getSelect()->__toString();die;
    	$data = [];

    	$collection2 = $this->_productCollectionFactory->create();
    	$collection2->setStoreId(0);
    	$collection2->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
    	$collection2->addAttributeToFilter([
	       ['attribute' => 'name', 'like' => '%'.$queryName.'%'],
	       ['attribute' => 'sku', 'like' => '%'.$queryName.'%']
	    ]);
    	$collection2->distinct(true);
    	$count = $collection2->count();
    	$media = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    	if($collection->count()){
    		foreach ($collection as $pro) {
    			if($pro->getThumbnail()){
    				$img = $media.'catalog/product'.$pro->getThumbnail();
    			}
    			else{
    				$img = $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail');
    			}
    			$data[] = ['id' => $pro->getId(), 'name' => $pro->getName(), 'sku' => $pro->getSku(), 'thumbnail' => $img];
    		}
    	}
        $result = ['items' => $data, 'total_count' => $count];
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}