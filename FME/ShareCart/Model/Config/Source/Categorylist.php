<?php 
namespace FME\ShareCart\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Categorylist implements ArrayInterface
{
    protected $_categoryHelper;

    public function __construct(
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_categoryHelper = $catalogCategory;
        $this->_storeManager = $storeManager;
    }

    /*
     * Return categories helper
     */

    private function getStoreData(){
        $storeManagerDataList = $this->_storeManager->getStores();
         $options = array();
         
         foreach ($storeManagerDataList as $key => $value) {
                   $options[] = ['label' => $value['name'].' - '.$value['code'], 'value' => $key];
         }
         return $options;
    }
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }

    /*  
     * Option getter
     * @return array
     */
    public function toOptionArray()
    {
      //  $arr1=$this->getStoreData();
      $array_final=array();

        $arr=array();
        $arr['label']="All Store";
        $arr['value']=0;
        array_push($array_final, $arr);
        foreach ($this->getStoreData() as $key => $value)
        {
            array_push($array_final,$value);
        }
        return $array_final;
    }

    /*
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {

        $categories = $this->getStoreCategories(true,false,true);

        $catagoryList = array();
        foreach ($categories as $category){

            $catagoryList[$category->getEntityId()] = '__'.($category->getName());
        }

        return $catagoryList;
    }

}