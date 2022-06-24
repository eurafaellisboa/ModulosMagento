<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Testimonial\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class Testimonial extends Template
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, array $data = [], \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        parent::__construct($context, $data);
		$this->_objectManager = $objectManager;
    }
	
	public function getModel(){
		return $this->_objectManager->create('MGS\Testimonial\Model\Testimonial');
	}
	
	public function getCollection(){
		$model = $this->getModel();
		$collection = $model->getCollection()
			->addFieldToFilter('status', 1)
			->setOrder('testimonial_id', 'DESC');
		if($this->hasData('testimonials_count')){
			$collection->setPageSize($this->getData('testimonials_count'));
		}
			
		return $collection;
	}
	
	public function getAvatarUrl($fileName){
		if($fileName){
			$path = 'testimonial' . '/' . $fileName;
			$avatarUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
			return $avatarUrl;
		}
		return false;
	}
}

