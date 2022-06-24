<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\MollaTheme\Block\Adminhtml\System;

/**
 * Export CSV button for shipping table rates
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Import extends \MGS\ThemeSettings\Block\Adminhtml\System\Import
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
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$activeKey = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('active_theme/activate/molla');
		
		$html = '<input type="file" id="fbuilder_import_file" name="import_file" accept="application/xml" style="margin-bottom:5px"';
		
		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= ' disabled="disabled"';
			}
		}
		
		$html .='/><br/><select id="fbuilder_import_page_id" name="groups[import][fields][page_id][value]" class="select admin__control-select" data-ui-id="select-groups-import-fields-page_id-value" style="width:210px; margin-right:10px"';
		
		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= ' disabled="disabled"';
			}
		}
		
		$html .= '><option value="">'.__('Choose Page to Import').'</option>';
		if(count($collection)>0){
			foreach($collection as $page){
				if($page->getId()){
					$html .= '<option value="'.$page->getId().'">'. $page->getTitle() . ' (ID:'.$page->getId().')' . '</option>';
				}
			}
		}
		
		$html .= '</select>';
		
		if($storeId = $this->_request->getParam('store')){
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/import", ['store'=>$storeId]);
		}elseif($websiteId = $this->_request->getParam('website')){
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/import", ['website'=>$websiteId]);
		}else{
			$url = $this->_backendUrl->getUrl("adminhtml/mollatheme/import");
		}

		$html .= '<button type="button" class="action-default scalable" data-ui-id="widget-button-2"';

		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= ' disabled="disabled"';
			}else{
				$html .= ' onclick="importPage(\''.$url.'\')"';
			}
		} else {
			$html .= ' onclick="importPage(\''.$url.'\')"';
		}
		
		$html .= '><span id="wait-text" style="display:none">'.__('Please wait...').'</span><span id="import-text">'.__('Import').'</span></button>';
		
		if(!$this->isLocalhost()) {
			if($activeKey==''){
				$html .= '<p style="margin-top:5px"><a href="'.$this->_backendUrl->getUrl('adminhtml/system_config/edit/section/active_theme').'" style="text-decoration:none; "><span style="color:#ff0000">'.__('Activation is required.').'</span></a></p>';
			}
		}

        return $html;
    }
}
