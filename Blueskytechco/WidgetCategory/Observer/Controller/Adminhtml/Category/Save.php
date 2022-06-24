<?php
namespace Blueskytechco\WidgetCategory\Observer\Controller\Adminhtml\Category;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Save implements ObserverInterface {
	
	protected $_imageUploader;

	public function __construct(
        \Blueskytechco\WidgetCategory\Model\ImageUploader $imageUploader
    ) {
    	$this->_imageUploader = $imageUploader;
    }

	public function execute(Observer $observer) {
		$var_request = $observer->getRequest();
		$category_mode = $observer->getCategory();
		$data_post = $var_request->getPostValue();
		$widget_category_thumbnail = '';
		
		if(isset($data_post['image']) && is_array($data_post['image'])){
			if(isset($data_post['image'][0]['url'])){
				$convert_image = $data_post['image'][0];
				$url_image = $data_post['image'][0]['url'];
				if(strpos($url_image, 'catalog/tmp') === false){
					$trim_media = explode("/media/", $url_image);
					$convert_image['url'] = "/media/".end($trim_media);
					$data_post['image'][0] = $convert_image;
					$category_mode->setImage($convert_image['url']);
				}
			}
        }
        
		if(isset($data_post['widget_category_thumbnail']) && is_array($data_post['widget_category_thumbnail'])){
			if(isset($data_post['widget_category_thumbnail'][0]['name'])){
				if(isset($data_post['widget_category_thumbnail'][0]['is_saved'])){
					$widget_category_thumbnail = $data_post['widget_category_thumbnail'][0]['name'];
				}
				else{
					$widget_category_thumbnail = $this->_imageUploader->moveFileFromTmp($data_post['widget_category_thumbnail'][0]['name']);
				}
			}
        }
		
        $category_mode->setWidgetCategoryThumbnail($widget_category_thumbnail);
	}
}