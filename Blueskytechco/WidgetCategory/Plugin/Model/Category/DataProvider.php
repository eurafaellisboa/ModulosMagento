<?php
 
namespace Blueskytechco\WidgetCategory\Plugin\Model\Category;
 
class DataProvider
{    
    protected $_storeManager;
    protected $_imageUploader;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blueskytechco\WidgetCategory\Model\ImageUploader $imageUploader
    ) {
        $this->_storeManager = $storeManager;
        $this->_imageUploader = $imageUploader;
    }

    public function afterGetData(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {    
        $process_result = $result;
        $catagory = $subject->getCurrentCategory();
        $widget_category_thumbnail = $catagory->getWidgetCategoryThumbnail();
        if(is_array($process_result) && !empty($process_result)){
            foreach($process_result as $key_p_r => $val_p_r){
                if($widget_category_thumbnail != ''){
                    $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
                    $convert_name[0]['name'] = $widget_category_thumbnail;
                    $convert_name[0]['url'] = $mediaUrl.$this->_imageUploader->getBasePath().'/'.$widget_category_thumbnail;
                    $convert_name[0]['is_saved'] = 1;
                    $process_result[$key_p_r]['widget_category_thumbnail'] = $convert_name;
                }
                break;
            }
        }
        return $process_result;
    }    
}
?>