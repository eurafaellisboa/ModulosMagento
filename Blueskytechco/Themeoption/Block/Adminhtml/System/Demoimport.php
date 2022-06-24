<?php

namespace Blueskytechco\Themeoption\Block\Adminhtml\System;

use Magento\Framework\App\Filesystem\DirectoryList;

class Demoimport extends \Magento\Config\Block\System\Config\Form\Fieldset
{
	protected $_getFile;
	protected $_geDir;
	protected $_themeFactory;
	protected $_resourceCon;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $auth,
        \Magento\Framework\View\Helper\Js $js,
		\Magento\Framework\Filesystem $file,
		\Magento\Theme\Model\ThemeFactory $themeFactory,
		\Magento\Framework\App\ResourceConnection $resourceCon,
        array $data = []
    ) {
        parent::__construct($context, $auth, $js, $data); 
		$this->_getFile = $file;
		$this->_resourceCon = $resourceCon;
		$this->_geDir = $this->_getFile->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('code/Blueskytechco/Themeoption');
		$this->_themeFactory = $themeFactory;
    }
	
	protected function _getHeaderCommentHtml($element)
    {
    	$load_parser_xml = new \Magento\Framework\Xml\Parser();
    	$data_theme = $load_parser_xml->load($this->_geDir.'/demo/themes.xml')->xmlToArray();
    	

    	$model_themes = $this->_themeFactory->create();
		$theme_collection = $model_themes->getCollection();
		$themes = [];
		if($theme_collection->count()){
			foreach ($theme_collection as $val_theme) {
				$codes = explode("/", $val_theme->getCode());
				$themes[$val_theme->getThemeId()] = end($codes);
			}
		}

		$store_id = ($this->getRequest()->getParam('store')) ? $this->getRequest()->getParam('store') : 'default';
		$website_id = ($this->getRequest()->getParam('website')) ? $this->getRequest()->getParam('website') : 'default';

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $this->_resourceCon;
        $connection = $resource->getConnection();
        $tableCoreConfigData = $resource->getTableName('core_config_data');
        $sql_default_theme = "SELECT * FROM ".$tableCoreConfigData." WHERE path LIKE '%web/default/cms_home_page%' AND scope = 'default'";
        $result_default_theme = $connection->fetchRow($sql_default_theme);
        
        $selected_theme_id = false;
        if(isset($result_default_theme['value'])){
        	$selected_theme_id = $result_default_theme['value'];
        }

        if($this->getRequest()->getParam('website')){
        	$sql_website_theme = "SELECT * FROM ".$tableCoreConfigData." WHERE path LIKE '%web/default/cms_home_page%' AND scope = 'websites' AND scope_id = ".$this->getRequest()->getParam('website');
        	$result_website_theme = $connection->fetchRow($sql_website_theme);
        	if(isset($result_website_theme['value'])){
	        	$selected_theme_id = $result_website_theme['value'];
	        }
        }

        if($this->getRequest()->getParam('store')){
        	$sql_store_theme = "SELECT * FROM ".$tableCoreConfigData." WHERE path LIKE '%web/default/cms_home_page%' AND scope = 'stores' AND scope_id = ".$this->getRequest()->getParam('store');
        	$result_store_theme = $connection->fetchRow($sql_store_theme);
        	if(isset($result_store_theme['value'])){
	        	$selected_theme_id = $result_store_theme['value'];
	        }
        }

    	if(isset($data_theme['root']['themes']['id'])){
    		$html = '<table class="form-list" id="table-container-blueskytechco-importer" cellspacing="0"><tbody>';
	        	$html .= '<tr><td style="padding: 0;">';
	        		$html .= '<h2 class="title-theme-name-fixed">'.$data_theme['root']['themes']['name'].'</h2>';
	        		$html .= '<p class="des-theme-name-fixed">'.$data_theme['root']['themes']['des'].'</p>';
	        		if(isset($data_theme['root']['themes']['home']) && is_array($data_theme['root']['themes']['home']) && !empty($data_theme['root']['themes']['home'])){
	        			$html .= '<div class="blueskytechco-container-import-theme-items">';
	        				foreach ($data_theme['root']['themes']['home'] as $val_item) {
	        					$theme_id_fixed = array_search('bluesky_kalles_default', $themes);
	        					$html .= '<div class="item-theme-import-fixed-container">
        								<div class="info-theme-screenshot">
        									<img src="//'.$val_item['image'].'" alt="'.$val_item['title'].'">
        								</div>';
        								if(!$selected_theme_id || $selected_theme_id != $val_item['identifier']){
        									$html .= '<div class="info-theme-name-and-activate">
	        									<h2 class="item-theme-name" id="'.$val_item['identifier'].'">'.$val_item['title'].'</h2>
	        									<div class="item-theme-actions">
	        										<a class="item-button-live-preview" target="_blank" href="//'.$val_item['linkpreview'].'">'.__("Live Preview").'</a>
	        										<a class="item-button-activate" href="#" data-theme-title="'.$val_item['title'].'" data-theme-id="'.$theme_id_fixed.'" data-theme-name="'.$val_item['identifier'].'">'.__("Activate").'</a>
	        									</div>
	        								</div>';
        								}
        								else{
	        								$html .= '<div class="info-theme-name-and-activate theme-has-selected-fix-style">
	        									<h2 class="item-theme-name">'.__('Active: ').$val_item['title'].'</h2>
	        								</div>';
        								}
	        					$html .= '</div>';
	        				}
	        			$html .= '</div>';
	        		}
	        	$html .= '</td></tr>';
			$html .= '</tbody></table>';
			$url_ajax = $this->getUrl("themeoption/demoimporter/runimport");
			$html .= '<script>    
					    require([
					        "jquery"
					    ], function($) {
					    	$(".item-button-activate").click(function(){
					    		var theme_name = $(this).attr("data-theme-title");
					    		var params = {id: "'.$data_theme['root']['themes']['id'].'",themetitle:  $(this).attr("data-theme-title"), themename: $(this).attr("data-theme-name"), themeid: $(this).attr("data-theme-id"), store: "'.$store_id.'", website: "'.$website_id.'"};
							  	$.ajax({
				                    url: "'.$url_ajax.'",
				                    data: params,
				                    type: "post",
				                    showLoader: true,
				                    dataType: "json",
				                    success: function (res) {
				                    	if(res.result == "success"){
				                    		alert(theme_name+" has been activated successfully!");
				                        	location.reload();
				                    	}
				                    	else{
				                    		alert("Your license is invalidated. Please go to: Blueskytechco > Blueskytechco Theme > Activation Purchase Code");
				                    	}
				                    },
				                    error: function (res) {
				                        alert("Error in sending ajax request");
				                    }
			                	});
							  	return false;
							});
					    });
					</script>';
    	}
    	else{
    		$html = '<table class="form-list" cellspacing="0"><tbody>';
	        	$html .= '<tr><td style="padding: 0;">';
	        		$html .= '<h2 class="title-theme-name-fixed">'.__("Can not get the data file.").'</h2>';
	        	$html .= '</td></tr>';
			$html .= '</tbody></table>';
    	}
		
        return $html;
    }
}
