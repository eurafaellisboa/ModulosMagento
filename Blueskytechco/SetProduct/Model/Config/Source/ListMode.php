<?php
namespace Blueskytechco\SetProduct\Model\Config\Source;

class ListMode implements \Magento\Framework\Option\ArrayInterface{

	protected $_resource;

    public function __construct(
		\Magento\Framework\App\ResourceConnection $resource
    ) {
		$this->_resource = $resource;;
    } 
	
	public function toOptionArray(){
		$adapter  = $this->_resource->getConnection();
		$tableName = $this->_resource->getTableName("blueskytechco_setproduct");
        $sql = "SELECT * FROM ".$tableName."";
        $data_query = $adapter->fetchAll($sql);
		$array = [];
		$array[] = ['value' => '0', 'label' => __('Root')]; 
		foreach ($data_query as $item) {
			$array[] = ['value' => ''.$item['entity_id'].'', 'label' => __(''.$item['name'].'')]; 
		}
		return $array;  
	}
}
