<?php
namespace Blueskytechco\CmsPageBanner\Observer\Controller\Adminhtml\Page;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Save implements ObserverInterface {
	
	protected $_imageUploader;

	public function __construct(
        \Blueskytechco\CmsPageBanner\Model\ImageUploader $imageUploader
    ) {
    	$this->_imageUploader = $imageUploader;
    }

	public function execute(Observer $observer) {
		$var_request = $observer->getRequest();
		$page_mode = $observer->getPage();
		$data_post = $var_request->getPostValue();
		$image_field = '';

		if(isset($data_post['image_field']) && is_array($data_post['image_field'])){
			if(isset($data_post['image_field'][0]['name'])){
				if(isset($data_post['image_field'][0]['is_saved'])){
					$image_field = $data_post['image_field'][0]['name'];
				}
				else{
					$image_field = $this->_imageUploader->moveFileFromTmp($data_post['image_field'][0]['name']);
				}
			}
        }
		
        $page_mode->setImageField($image_field);
	}
}