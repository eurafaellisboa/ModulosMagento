<?php
namespace Blueskytechco\SizeChart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;

class LayoutLoadBefore implements ObserverInterface
{
	
	protected $_resource;
    protected $_registry;
	protected $_scopeConfig;
	protected $request;
	protected $_storeManager;    
	protected $_sizechart;   

    public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Page\Config $pageConfig,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Blueskytechco\SizeChart\Model\SizeChart $sizechart,
        Registry $registry
    ) {
        $this->_registry = $registry;
		$this->request = $request;
		$this->_pageConfig = $pageConfig;
		$this->_scopeConfig = $scopeInterface;
		$this->_storeManager = $storeManager;
		$this->_sizechart = $sizechart;
		$this->_resource = $resource;				
    }

    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
		$params = $this->request->getParams();
        if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'categorytab/category/view') !== false || strpos($_SERVER['REQUEST_URI'], 'blueskytechco_quickview/product/view') !== false){
            return $this;
        } 
		
		/* Product page */
		$product = $this->_registry->registry('product');
		if ($action == 'catalog_product_view' && $product) {
			$adapter  = $this->_resource->getConnection();
			$tableName = $this->_resource->getTableName("size_chart");
			$product_sizechart = $product->getData('product_sizechart');
			$category_ids = $product->getCategoryIds();
			$store_id = $this->_storeManager->getStore()->getId();
			
			if ($product_sizechart) {
				$sql = "SELECT * FROM ".$tableName ." WHERE entity_id='$product_sizechart'";
				$data = $adapter->fetchRow($sql);
				if($data){
					if($data['store_id'] == $store_id || $data['store_id'] == 0){
						if($data['type'] == 1){
							$layout = $observer->getData('layout');
							$layout->getUpdate()->addHandle('catalog_product_view_sizechart_tab');
						}else{
							$layout = $observer->getData('layout');
							$layout->getUpdate()->addHandle('catalog_product_view_sizechart_popup');
						}
					}
				}else{
					$product_sizechart = '';
					$sizecharts = $this->_sizechart->getCollection();
					foreach ($sizecharts as $sizechart) {
						$category_sizechart = $sizechart->getCategoryIds();
						$category_sizechart = explode(",",$category_sizechart);
						foreach ($category_ids as $category){
							if (in_array($category, $category_sizechart)) {
								$product_sizechart = $sizechart->getId();
								break;
							}
						}
						if($product_sizechart){
							break;
						}
					}
					$sql = "SELECT * FROM ".$tableName ." WHERE entity_id='$product_sizechart'";
					$data = $adapter->fetchRow($sql);
					if($data){
						if($data['store_id'] == $store_id || $data['store_id'] == 0){
							if($data['type'] == 1){
								$layout = $observer->getData('layout');
								$layout->getUpdate()->addHandle('catalog_product_view_sizechart_tab');
							}else{
								$layout = $observer->getData('layout');
								$layout->getUpdate()->addHandle('catalog_product_view_sizechart_popup');
							}
						}
					}
				}
			} else {
				$product_sizechart = '';
				$sizecharts = $this->_sizechart->getCollection();
				foreach ($sizecharts as $sizechart) {
					$category_sizechart = $sizechart->getCategoryIds();
					$category_sizechart = explode(",",$category_sizechart);
					foreach ($category_ids as $category){
						if (in_array($category, $category_sizechart)) {
							$product_sizechart = $sizechart->getId();
							break;
						}
					}
					if($product_sizechart){
						break;
					}
				}
				$sql = "SELECT * FROM ".$tableName ." WHERE entity_id='$product_sizechart'";
				$data = $adapter->fetchRow($sql);
				if($data){
					if($data['store_id'] == $store_id || $data['store_id'] == 0){
						if($data['type'] == 1){
							$layout = $observer->getData('layout');
							$layout->getUpdate()->addHandle('catalog_product_view_sizechart_tab');
						}else{
							$layout = $observer->getData('layout');
							$layout->getUpdate()->addHandle('catalog_product_view_sizechart_popup');
						}
					}
				}
			}
			
			return $this;
		}
		
        return $this;
    }
}
?>