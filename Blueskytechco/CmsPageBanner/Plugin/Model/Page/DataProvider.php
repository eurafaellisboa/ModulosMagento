<?php
 
namespace Blueskytechco\CmsPageBanner\Plugin\Model\Page;
 
class DataProvider
{    
    protected $_storeManager;
    protected $_imageUploader;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Blueskytechco\CmsPageBanner\Model\ImageUploader $imageUploader
    ) {
        $this->_storeManager = $storeManager;
        $this->_imageUploader = $imageUploader;
    }

    public function afterGetData(\Magento\Cms\Model\Page\DataProvider $subject, $result)
    {    
        $process_result = $result;
        if(is_array($process_result) && !empty($process_result)){
            foreach($process_result as $key_p_r => $val_p_r){
                if(isset($process_result[$key_p_r]['image_field']) && $process_result[$key_p_r]['image_field'] != ''){
                    $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
                    $image_field = $process_result[$key_p_r]['image_field'];
                    $convert_name[0]['name'] = $image_field;
                    $convert_name[0]['url'] = $mediaUrl.$this->_imageUploader->getBasePath().'/'.$image_field;
                    $convert_name[0]['is_saved'] = 1;
                    $process_result[$key_p_r]['image_field'] = $convert_name;
                }
            }
        }
       
        return $process_result;
    }    
}
?>