<?php
/**
 * Copyright © 2019 Blueskytechco. All rights reserved.
 */

namespace Blueskytechco\SetProduct\Block\Adminhtml\ProductSet\Helper;

use \Magento\Framework\Data\Form\Element\AbstractElement;
use \Magento\Framework\Data\Form\Element\Factory;
use \Magento\Framework\Data\Form\Element\CollectionFactory;
use \Magento\Framework\Escaper;


class Image extends AbstractElement
{
    /**
     * @var \Blueskytechco\StoreLocator\Helper\Config 
     */
	private $timeHours;
	protected $_storeManager;

    /**
     * @param Factory              $factoryElement
     * @param CollectionFactory    $factoryCollection
     * @param Escaper              $escaper
     * @param ConfigHelper         $configHelper
     * @param array                $data 
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        Escaper $escaper,
        array $data = []
    ) {
		$this->_storeManager = $storeManager;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Return the element as HTML
     *
     * @return string
     */
    public function getElementHtml() 
    {
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager  = $_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$registry = $_objectManager->get('Magento\Framework\Registry');
		$rule = $registry->registry('blueskytechco_setproduct');
		$data_rule = $rule->getData();
		$mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$html = '';
		$html .= '<div class="admin__field-label"><div class="label">Preview Product Set</div><div class="button-reload"><button type="button" title="Add" id="fixed-button-add-update-click">Reload Item</button></div></div>';
		$html .= '<div class="admin__field-preview" style="position: relative;">';
			$html .= '<div class="add-image-preview">';
				if(isset($data_rule['banner_image']) && $data_rule['banner_image'] != ''){
					$html .= '<img src="'.$mediaUrl.$data_rule['banner_image'].'">';
				}
			$html .= '</div>';
			if(isset($data_rule['product_data']) && $data_rule['product_data'] != ''){
				$product_data = json_decode($data_rule['product_data'],true);
				foreach ($product_data as $key => $val) {
					$index = $key + 10;
					$html .= '<div class="rows-fixed" style="'.$val['css'].'">'.$val['text'].'<input name="product[product_data_new]['.$index.'][sku]" value="'.$val['sku'].'" type="hidden"><input name="product[product_data_new]['.$index.'][text]" value="'.$val['text'].'" type="hidden"><input class="product_data_css" name="product[product_data_new]['.$index.'][css]" value="'.$val['css'].'" type="hidden"><input name="product[product_data_new]['.$index.'][display]" value="'.$val['display'].'" type="hidden"></div>';
				}
			}
        $html .= '</div>';
		$html .= $this->getAfterElementHtml();

        return $html;
    }
}
