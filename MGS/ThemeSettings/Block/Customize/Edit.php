<?php

nameSpace MGS\ThemeSettings\Block\Customize;

use Magento\Framework\App\Filesystem\DirectoryList;
class Edit  extends \Magento\Framework\View\Element\Template {
	/**
     * @var \Magento\Framework\App\RequestInterface
     */
	protected $request;
	
	/**
     * @var \Magento\Framework\View\Design\Theme\ThemeProviderInterface
     */
	protected $_themeProvider;
	
	/**
     * @var \MGS\ThemeSettings\Helper\Config
     */
	protected $_themeSettingConfig;
	
	/**
     * @var \Magento\Framework\Filesystem
     */
	protected $_filesystem;
	
	/**
	 * @var \Magento\Framework\Xml\Parser
	 */
	protected $_parser;
	protected $_count;
	protected $_settingPath;
	protected $_ioFile;
	protected $_storeManager;
	protected $session;
	
	
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider
     * @param \MGS\ThemeSettings\Helper\Config $themeSettingConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Xml\Parser $filesystem
     * @param array $data
     */
    public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
		\MGS\ThemeSettings\Helper\Config $themeSettingConfig,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Xml\Parser $parser,
		\Magento\Framework\Filesystem\Io\File $ioFile,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Session\SessionManagerInterface $session,
		 array $data = []
    ) {
		parent::__construct($context, $data);
        $this->_urlBuilder = $context->getUrlBuilder();
		$this->request = $request;
		$this->_themeProvider = $themeProvider;
		$this->_themeSettingConfig = $themeSettingConfig;
		$this->_filesystem = $filesystem;
		$this->_parser = $parser;
		$this->_storeManager = $storeManager;
		$this->_count = 0;
		$this->_settingPath = [];
		$this->_ioFile = $ioFile;
		$this->session = $session;
    }

	/* Decode Referrer Url */
	public function getUrlDecode(){
		if($referrer = $this->request->getParam('referrer')){
			$url = base64_decode(strtr($referrer, '-_,', '+/='));
			return $this->_urlBuilder->sessionUrlVar($url);
		}else{
			return $this->_urlBuilder->getUrl();
		}
	}
	
	/* Get current theme */
	public function getTheme()
	{
		$themeId = $this->_themeSettingConfig->getStoreConfig(\Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID);

		/** @var $theme \Magento\Framework\View\Design\ThemeInterface */
		$theme = $this->_themeProvider->getThemeById($themeId);
		
		return $theme;
	}
	
	/* Get customize setting from xml file */
	public function getCustomizeSetting(){
		$html = '<div class="left-panel-content"><div class="content"><ul class="setting-list">';
		
		$themePath = $this->getTheme()->getThemePath();
		$customizeFolder = 'design/frontend/'.$themePath.'/etc/customize/';
		$defaultFilePath = $customizeFolder.'default.xml';
		$defaultFile = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath($defaultFilePath);
		$arrSetting = [];
		if (is_readable($defaultFile)){
			$defaultContent = $this->_parser->load($defaultFile)->xmlToArray();
			$arrSetting = $defaultContent['settings'];
		}
		
		
		$specialPageFilePath = $customizeFolder.$this->getCurrentPage().'.xml';
		$specialPageFile = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath($specialPageFilePath);
		if (is_readable($specialPageFile)){
			$specialPageContent = $this->_parser->load($specialPageFile)->xmlToArray();
			
			if(isset($specialPageContent['settings']['section'][0]['name'])){
				foreach($specialPageContent['settings']['section'] as $section){
					$arrSetting['section'][] = $section;
				}
			}else{
				$arrSetting['section'][] = $specialPageContent['settings']['section'];
			}
		}

		$this->_count = 0;
		//echo '<pre>'; print_r($arrSetting); echo '</pre>'; die();
		if(count($arrSetting['section'])>0){
			foreach($arrSetting['section'] as $section){
				if(isset($section['group'][0]['name']) && isset($section['group'][0]['title']) && isset($section['group'][0]['icon'])){
					foreach($section['group'] as $group){
						$this->_count++;
						$html .= $this->getTitleHtml($this->_count, $group);
					}
				}else{
					
					$this->_count++;
					$html .= $this->getTitleHtml($this->_count, $section['group']);
				}
			}
		}
		
		
		
		$html .= '</ul></div></div>';
		$html .= '<div class="panel-form-fields';
		if($this->getRequest()->getParam('activepanel')!=0){
			$html .= ' active-panel';
		}
		$html .= '" id="panel-form">';
		
		
		$this->_count = 0;
		if(count($arrSetting['section'])>0){
			foreach($arrSetting['section'] as $section){
				if(isset($section['group'][0]['name']) && isset($section['group'][0]['title']) && isset($section['group'][0]['icon'])){
					foreach($section['group'] as $group){
						$this->_count++;
						$html .= $this->getContentHtml($this->_count, $section['name'], $group);
					}
				}else{
					$this->_count++;
					$html .= $this->getContentHtml($this->_count, $section['name'], $section['group']);
					//echo '<pre>'; print_r($section); echo '</pre>';
				}
			}
		}
		
		$html .= '</div>';
		
		$this->generateSettingTemp();
		
		return $html;
	}
	
	public function getCurrentPage(){
		$fullActionName = $this->session->getFrameFullActionName();
		
		if(($fullActionName=='customer_account_index') || 
			($fullActionName=='customer_account_edit') ||
			($fullActionName=='customer_address_index') ||
			($fullActionName=='gdpr_customer_index') ||
			($fullActionName=='downloadable_customer_products') ||
			($fullActionName=='sales_order_history') ||
			($fullActionName=='sales_order_view') ||
			($fullActionName=='wishlist_index_index') ||
			($fullActionName=='newsletter_manage_index') ||
			($fullActionName=='vault_cards_listaction') ||
			($fullActionName=='paypal_billing_agreement_index') ||
			($fullActionName=='review_customer_index') ||
			($fullActionName=='review_customer_view')
		){
			return 'customer_account';
		}
		return $fullActionName;
	}
	
	/* Setting Title List By Page */
	public function getTitleSetting($settings){
		$html = '';
		if(isset($settings['settings']) && (count($settings['settings'])>0)){
			foreach($settings['settings'] as $section){
				if(isset($section['group'][0]['name']) && isset($section['group'][0]['title']) && isset($section['group'][0]['icon'])){
					foreach($section['group'] as $group){
						$this->_count++;
						$html .= $this->getTitleHtml($this->_count, $group);
					}
				}else{
					$this->_count++;
					$html .= $this->getTitleHtml($this->_count, $section['group']);
				}
			}
		}
		return $html;
	}
	
	public function getTitleHtml($number, $group){
		return '<li>
			<button class="no-btn panel-title-btn" data-number="'.$number.'" type="button"><span class="'.$group['icon'].' panel-icon"></span><span>'.__($group['title']).'</span></button>
		</li>';
	}
	
	/* Setting Content Setting By Page */
	public function getSettingContent($settings){
		$html = '';
		if(isset($settings['settings']) && (count($settings['settings'])>0)){
			foreach($settings['settings'] as $section){
				if(isset($section['group'][0]['name']) && isset($section['group'][0]['title']) && isset($section['group'][0]['icon'])){
					foreach($section['group'] as $group){
						$this->_count++;
						$html .= $this->getContentHtml($this->_count, $section['name'], $group);
					}
				}else{
					$this->_count++;
					$html .= $this->getContentHtml($this->_count, $section['name'], $section['group']);
				}
				
			}
		}
		return $html;
	}
	
	public function getContentHtml($number, $section, $group){
		$html = '<div class="panel-form-group" id="panel'.$number.'"';
		
		if($this->getRequest()->getParam('activepanel')!=0 && ($this->_count==$this->getRequest()->getParam('activepanel'))){
			$html .= ' style="display:block"';
		}else{
			$html .= ' style="display:none"';
		}
		
		$html .= '><header class="panel-header">
			<button class="no-btn btn-close-panel" type="button"><span class="pe-7s-angle-left panel-icon"></span></button>
			<h2 class="active-title"><span>'.__($group['title']).'</span></h2>
		</header>
		<div class="form-fields">';

		if(isset($group['fields'][0]['item'])){
			foreach($group['fields'] as $field){
				$html .= '<div class="field-group">';
				if(isset($field['label']) && $field['label']!=''){
					$html .= '<h3>'.$field['label'].'</h3>';
				}
				
				if(isset($field['item'][0]['name'])){
					foreach($field['item'] as $item){
						$html .= $this->getFormFieldHtml($section, $group['name'], $item);
					}
				}else{
					if(isset($group['fields'][0]['item']['name'])){
						$html .= $this->getFormFieldHtml($section, $group['name'], $group['fields'][0]['item']);
					}

				}
				
				
				$html .= '</div>';
			}
		}else{
			$html .= '<div class="field-group">';
			if(isset($group['fields']['label']) && $group['fields']['label']!=''){
				$html .= '<h3>'.$group['fields']['label'].'</h3>';
			}
			
			if(isset($group['fields']['item'][0])){
				foreach($group['fields']['item'] as $item){
					$html .= $this->getFormFieldHtml($section, $group['name'], $item);
				}
			}else{
				$html .= $this->getFormFieldHtml($section, $group['name'], $group['fields']['item']);
			}

			$html .= '</div>';
		}
		$html .= '</div></div>';
		
		return $html;
	}
	
	/* Html Field */
	public function getFormFieldHtml($section, $group, $item){
		if(!isset($item['page']) || (isset($item['page']) && ($item['page'] == $this->session->getFrameFullActionName()))){
			$fieldId = $section.'_'.$group.'_'.$item['name'];
			$fieldValue = $this->_themeSettingConfig->getStoreConfig($section.'/'.$group.'/'.$item['name']);
			$this->_settingPath[$fieldId] =  ['path'=>$section.'/'.$group.'/'.$item['name'], 'value'=>$fieldValue];
			if(isset($item['custom']) && ($item['custom']!='')){
				$fieldId = $item['name'];
				$fieldValue = $this->_themeSettingConfig->getStoreConfig($item['custom']);
				$this->_settingPath[$fieldId] =  ['path'=>$item['custom'], 'value'=>$fieldValue];
			}
			
			$html = '<div id="'.$fieldId.'_field" class="field field-'.$item['type'].'"';
			$htmlData = ' data-reload="1"';
			if(isset($item['noreload']) && ($item['noreload']==1)){
				$htmlData = ' data-reload="0"';
			}
			
			if(isset($item['load_style']) && ($item['load_style']==1)){
				$htmlData .= ' data-load-style="1"';
			}
			
			if(isset($item['depends']) && isset($item['depends']['condition']) && $item['depends']['condition']!=''){
				$condition = str_replace('"',"'",str_replace(' ','',$item['depends']['condition']));
				$condition = str_replace('&&','_and_',$condition);
				$condition = str_replace('||','_or_',$condition);
				
				$html .= ' data-depend="'.$condition.'"';
			}

			$html .= '>';
			if($item['type']=='boolean'){
				$html .= '<label for="'.$fieldId.'_temp">'.__($item['label']).'</label>';
			}else{
				$html .= '<label for="'.$fieldId.'" class="field-label">'.__($item['label']).'</label>';
			}
			//$html .= '<div class="input">';
			switch ($item['type']) {
				case 'text':
					$html .= '<input class="input-text panel-input" type="text" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'" value="'.$fieldValue.'"'.$htmlData.'/>';
					break;
				case 'number':
					$html .= '<input class="input-text validate-number panel-input" type="text" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'" value="'.$fieldValue.'"'.$htmlData.'/>';
					break;
				case 'color':
					$html .= '<input class="input-text panel-input panel-input-color" type="text" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'" value="'.$fieldValue.'"'.$htmlData.'/>';
					break;
				case 'textarea':
				case 'editor':
					$html .= '<textarea class="input-text panel-input textarea" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'"'.$htmlData.'>'.$fieldValue.'</textarea>';
					break;
				case 'select':
					$html .= '<select class="select panel-input" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'"'.$htmlData.'>';
					if(isset($item['option']) && $item['option']!=''){
						if(class_exists($item['option'])){
							if((method_exists($item['option'],'toOptionArray'))){
								$optionClass = new $item['option'];
								$options = $optionClass->toOptionArray();
								foreach($options as $option){
									$html .= '<option value="'.$option['value'].'"';
									if($fieldValue == $option['value']){
										$html .= ' selected="selected"';
									}
									$html .= '>'.$option['label'].'</option>';
								}
							}
						}
					}
					$html .= '</select>';
					break;
				case 'multiselect':
					$arrValue = [];
					if($fieldValue!=''){
						$arrValue = explode(',',$fieldValue);
					}
					if(isset($item['option']) && $item['option']!=''){
						if(class_exists($item['option'])){
							if((method_exists($item['option'],'toOptionArray'))){
								$optionClass = new $item['option'];
								$options = $optionClass->toOptionArray();
								foreach($options as $option){
									$html .= '<div class="multi-field field-boolean">';
									$html .= '<label for="'.$fieldId.'_'.$option['value'].'">'.$option['label'].'</label>';
									$html .= '<input class="checkbox multiple-checkbox" value="'.$option['value'].'" data-parent="'.$fieldId.'" type="checkbox" id="'.$fieldId.'_'.$option['value'].'"';
									if(in_array($option['value'], $arrValue)){
										$html .= ' checked="checked"';
									}
									$html .= '/></div>';
								}
							}
						}
					}
					$html .= '<input class="input-text panel-input" type="hidden" value="'.$fieldValue.'" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'"'.$htmlData.'/>';
					break;
				case 'boolean':
					$html .= '<input class="checkbox checkbox-temp" type="checkbox" id="'.$fieldId.'_temp"';
					if($fieldValue == 1){
						$html .= ' checked="checked"';
					}
					$html .= '/>';
					
					$html .= '<input class="input-text panel-input" type="hidden" value="'.$fieldValue.'" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'"'.$htmlData.'/>';
					break;
				
				case 'image':
					$html .= '<div class="img-picker">';

					$path = 'default';
					if($fieldValue!=''){
						$arrValue = explode('/',$fieldValue);
						$removed = array_pop($arrValue);
						if(count($arrValue)>0){
							$path = implode('/',$arrValue);
						}else{
							$path = $arrValue[0];
						}
						
						$src = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).$item['path'].'/'.$fieldValue;
					}
					
					if(!isset($item['path'])){
						$item['path'] = '';
					}
					
					$inputFile = '<input type="file" class="input-image" data-type="image" data-store-path="'.$path.'" data-save-path="'.$item['path'].'" name="settings['.$section.']['.$group.']['.$item['name'].']" data-id="'.$fieldId.'"'.$htmlData;

					if(isset($item['allow_extensions']) && ($item['allow_extensions']!='')){
						$accept = str_replace(' ','',$item['allow_extensions']);
						$accept = str_replace(',',',.',$accept);
						$accept = '.'.$accept;
						$inputFile .= ' accept="'.$accept.'"';
					}
					$inputFile .= '/>';
					
					$html .= '<div class="img-preview">';
					
					if($fieldValue!=''){
						$html .= '<img src="'.$src.'" alt=""/>';
					}
					
					$html .= '<div class="select-img"';
					
					if($fieldValue!=''){
						$html .= ' style="display:none"';
					}
					
					$html .='>'.$inputFile.'<button class="btn-button-select" type="button" name="button"><span>'.__('Select image').'</span></button></div></div>';
					$html .= '<div class="img-action"';
					
					if($fieldValue==''){
						$html .= ' style="display:none"';
					}
					
					$html .= '>';
					
					$html .= $inputFile;
					
					$html .= '<button class="btn-button-file" type="button" name="button"><span>'.__('Change').'</span></button>
					<button class="btn-button-remove" type="button" data-type="image" name="button" data-element="'.$fieldId.'"'.$htmlData;

					$html .= '><span>'.__('Remove').'</span></button>
					</div>';
					
					
					$html .= '</div>';
					break;
				
				case 'file':
					$html .= '<div class="img-picker">';

					$path = 'default';
					if($fieldValue!=''){
						$arrValue = explode('/',$fieldValue);
						$removed = array_pop($arrValue);
						if(count($arrValue)>0){
							$path = implode('/',$arrValue);
						}else{
							$path = $arrValue[0];
						}
						
						$src = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).$item['path'].'/'.$fieldValue;
					}
					
					if(!isset($item['path'])){
						$item['path'] = '';
					}
					
					$inputFile = '<input type="file" class="input-image" data-type="file" data-store-path="'.$path.'" data-save-path="'.$item['path'].'" name="settings['.$section.']['.$group.']['.$item['name'].']" data-id="'.$fieldId.'"'.$htmlData;

					if(isset($item['allow_extensions']) && ($item['allow_extensions']!='')){
						$accept = str_replace(' ','',$item['allow_extensions']);
						$accept = str_replace(',',',.',$accept);
						$accept = '.'.$accept;
						$inputFile .= ' accept="'.$accept.'"';
					}
					$inputFile .= '/>';
					
					$html .= '<div class="img-preview">';
					
					if($fieldValue!=''){
						$arrFieldValue = explode('.',$fieldValue);
						$fileName = explode('/',$fieldValue);
						$html .= '<div class="icon-container"><span class="file-icon"><span>'.end($arrFieldValue).'</span></span><span class="text">'.end($fileName).'</span></div>';
					}
					
					$html .= '<div class="select-img"';
					
					if($fieldValue!=''){
						$html .= ' style="display:none"';
					}
					
					$html .='>'.$inputFile.'<button class="btn-button-select" type="button" name="button"><span>'.__('Select file').'</span></button></div></div>';
					$html .= '<div class="img-action"';
					
					if($fieldValue==''){
						$html .= ' style="display:none"';
					}
					
					$html .= '>';
					
					$html .= $inputFile;
					
					$html .= '<button class="btn-button-file" type="button" name="button"><span>'.__('Change').'</span></button>
					<button class="btn-button-remove" type="button" data-type="file" name="button" data-element="'.$fieldId.'"'.$htmlData;

					$html .= '><span>'.__('Remove').'</span></button>
					</div>';
					
					
					$html .= '</div>';
					break;
				
				case 'range':
					if(isset($item['min_max'])){
						$minMax = str_replace('-',':',$item['min_max']);
						$minMax = str_replace('_',':',$minMax);
						$minMax = explode(':',$minMax);
						if(isset($minMax[0]) && isset($minMax[1])){
							if($fieldValue==''){
								$fieldValue = 0;
							}
							$html .= '<div class="slidecontainer"><div class="slider-input">
							  <input type="range" min="'.$minMax[0].'" max="'.$minMax[1].'" value="'.$fieldValue.'" class="slider" data-input="'.$fieldId.'" step="'.$item['step'].'"/><input class="input-text panel-input" type="hidden" value="'.$fieldValue.'" name="settings['.$section.']['.$group.']['.$item['name'].']" id="'.$fieldId.'"'.$htmlData.'/></div>
							  <div class="value"><span>'.$fieldValue.'</span>';
							  if(isset($item['unit'])){
								  $html .= $item['unit'];
							  }
							  
							  $html .= '</div></div>';
						}
					}
					break;
			}
			$html .= '</div>';
			return $html;
		}
		return;
	}
	
	public function generateSettingTemp(){
		if($this->_themeSettingConfig->getStoreConfig('themesettings/customie_theme/temp_type')=='file'){
			$this->generateXmlFile();
		}else{
			$this->generateSession();
		}
		
	}
	
	public function generateXmlFile(){
		$path = 'mgs/customize/'.$this->_storeManager->getStore()->getId();
		$fullPath = $path.'/settings.xml';
		$filePath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($fullPath);
		if (!is_readable($filePath)){
			if(count($this->_settingPath)>0){
				$html = "<settings>\n";
				foreach($this->_settingPath as $path=>$value){
					$html .= "\t<".$path.">\n";
					$html .= "\t\t<path><![CDATA[".$value['path']."]]></path>\n";
					$html .= "\t\t<value><![CDATA[".$value['value']."]]></value>\n";
					$html .= "\t</".$path.">\n";
				}
				$html .= "</settings>";

				$io = $this->_ioFile;
				$io->setAllowCreateFolders(true);
				$io->open(array('path' => $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path)));
				$io->write($filePath, $html, 0644);
				$io->streamClose();
			}
		}else{
			$tempSetting = $this->_parser->load($filePath)->xmlToArray();
			if($this->_settingPath != $tempSetting['settings']){
				$html = "<settings>\n";
				foreach($this->_settingPath as $path=>$value){
					$html .= "\t<".$path.">\n";
					$html .= "\t\t<path><![CDATA[".$value['path']."]]></path>\n";
					$html .= "\t\t<value><![CDATA[".$value['value']."]]></value>\n";
					$html .= "\t</".$path.">\n";
				}
				$html .= "</settings>";

				$io = $this->_ioFile;
				$io->setAllowCreateFolders(true);
				$io->open(array('path' => $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path)));
				$io->write($filePath, $html, 0644);
				$io->streamClose();
			}
		}
	}
	
	public function generateSession(){
		$storeId = $this->_storeManager->getStore()->getId();
		$sessionSetting = $this->session->getSessionSetting();
		if($sessionSetting && isset($sessionSetting[$storeId])){
			if($this->_settingPath != $sessionSetting[$storeId]){
				$setting[$storeId] = $this->_settingPath;
				$this->session->setSessionSetting(false);
				$this->session->setSessionSetting($setting);
			}
		}else{
			if(count($this->_settingPath)>0){
				$setting[$storeId] = $this->_settingPath;
				$this->session->setSessionSetting($setting);
			}
		}
	}
} 