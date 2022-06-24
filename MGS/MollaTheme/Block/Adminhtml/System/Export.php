<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\MollaTheme\Block\Adminhtml\System;

/**
 * Export CSV button for shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Export extends \MGS\ThemeSettings\Block\Adminhtml\System\Export
{
	
	public function isLocalhost() {
        $whitelist = array(
            '127.0.0.1',
			'localhost',
			'127.0.0.1:8080',
			'localhost:8080',
            '::1'
        );
        
        return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
    }
	
    /**
     * @return string
     */
    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
		$collection = $this->collectionFactory->create();
		$collection->getSelect()->group('page_id');
		//echo $collection->getSelect(); die();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$activeKey = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('active_theme/activate/molla');
		
		$html = '<select id="fbuilder_export_page_id" name="groups[export][fields][page_id][value]" class="select admin__control-select" data-ui-id="select-groups-export-fields-page_id-value" style="width:210px; margin-right:10px"';
		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= ' disabled="disabled"';
			}
		}
		$html .= '><option value="">'.__('Choose Page to Export').'</option>';
		if(count($collection)>0){
			foreach($collection as $section){
				$pageId = $section->getPageId();
				$page = $this->_pageFactory->create()->load($pageId);
				if($page->getId()){
					$html .= '<option value="'.$page->getId().'">'. $page->getTitle() .'</option>';
				}
			}
		}
		
		$html .= '</select>';
		
		
		
		if($storeId = $this->_request->getParam('store')){
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/export", ['store'=>$storeId]);
		}elseif($websiteId = $this->_request->getParam('website')){
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/export", ['website'=>$websiteId]);
		}else{
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/export");
		}

		$html .= '<button type="button" class="action-default scalable" data-ui-id="widget-button-0"';
		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= ' disabled="disabled"';
			}else{
				$html .= ' onclick="exportPage(\''.$url.'\')"';
			}
		} else {
			$html .= ' onclick="exportPage(\''.$url.'\')"';
		}
		$html .= '><span>'.__('Export').'</span></button>';
		
		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= '<p style="margin-top:5px"><a href="'.$this->_backendUrl->getUrl('adminhtml/system_config/edit/section/active_theme').'" style="text-decoration:none; "><span style="color:#ff0000">'.__('Activation is required.').'</span></a></p>';
			}
		}

        return $html;
    }
}
