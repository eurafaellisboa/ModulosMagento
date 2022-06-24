<?php
namespace Blueskytechco\SizeChart\Helper;

use Magento\Framework\Registry;
use Magento\Cms\Model\Template\FilterProvider;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\Blueskytechco\SizeChart\Model\SizeChart $sizechart,
        FilterProvider $_filterProvider,
		Registry $registry
    ) {
		$this->_sizechart = $sizechart;
        $this->_filterProvider = $_filterProvider;
		$this->_resource = $resource;		
		$this->_registry = $registry;		
        parent::__construct($context);
    }

    public function getBlockContent($content = '')
    {
        if (!$this->_filterProvider) {
            return $content;
        }
        return $this->_filterProvider->getBlockFilter()->filter(trim($content));
    }
    
    public function getData($product)
    {
		if($product->getTypeId() == 'giftcard'){
			return false;
		}
        $adapter  = $this->_resource->getConnection();
		$tableName = $this->_resource->getTableName("size_chart");
		$product_sizechart = $product->getData('product_sizechart');
		$category_ids = $product->getCategoryIds();
		if($product_sizechart){
			$sql = "SELECT * FROM ".$tableName ." WHERE entity_id='$product_sizechart'";
			$data = $adapter->fetchRow($sql);
			if($data){
				return $this->getBlockContent($data['content']);
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
					return $this->getBlockContent($data['content']);
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
				return $this->getBlockContent($data['content']);
			}
		}
		return false;
    }
}
