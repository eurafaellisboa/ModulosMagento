<?php

namespace Blueskytechco\SetProduct\Block\Adminhtml\ProductSet\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
	protected $_storeManager;
    
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    public function render(\Magento\Framework\DataObject $row)
    {
        $html = '';
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if($image = $row->getBannerImage()){
            if (strpos($image, 'placehold.jp') === false) {
                $html = '<img style="width: 100%;max-width: 200px;" src="' . $mediaUrl . $image . '" />';
            }
            else{
                $html = '<img style="width: 100%;max-width: 200px;" src="' . $image . '" />';
            }
        }
        return $html;
    }
}
