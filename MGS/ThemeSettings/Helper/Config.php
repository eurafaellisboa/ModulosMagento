<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\ThemeSettings\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Contact base helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_storeManager;
	protected $_date;
	protected $_filter;
	protected $_url;
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	protected $_fullActionName;
	protected $_request;
	protected $_currentCategory;
	protected $_filesystem;
	
	
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Url $url
	) {
		$this->scopeConfig = $context->getScopeConfig();
		$this->_storeManager = $storeManager;
		$this->_objectManager = $objectManager;
		$this->_date = $date;
		$this->_request = $request;
		$this->_filesystem = $filesystem;
		$this->_filter = $context->getFilterManager();
		$this->_url = $url;
		
		$this->_fullActionName = $this->_request->getFullActionName();
		if($this->_request->getFullActionName() == 'catalog_category_view'){
			$this->_currentCategory = $this->getCurrentCategory();
		}
		
	}
	public function getModel($model){
		return $this->_objectManager->create($model);
	}
	public function getStore(){
		return $this->_storeManager->getStore();
	}
	
	/* Get system store config */
	public function getStoreConfig($node, $storeId = NULL){
		if($storeId != NULL){
			return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
		}
		return $this->scopeConfig->getValue($node, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
	/* Get curent date time */
	public function getCurrentDateTime(){
		$now = $this->_date->gmtDate();
		return $now;
	}
	
	public function getPercent($product){
		$price = $product->getOrigData('price');
		$finalPrice = $product->getFinalPrice();
		
		$save = $price - $finalPrice;
		$percent = round(($save * 100) / $price);
		
		return $percent;
	}
	
	public function getProductLabel($product){
		$html = '';
		$newLabel = $this->getStoreConfig('themesettings/catalog/new_label');
        $saleLabel = $this->getStoreConfig('themesettings/catalog/sale_label');
		$soldLabel = __('Out of Stock');
		// Out of stock label
		if (!$product->isSaleable()){
			$html .= '<span class="product-label sold-out-label"><span>'.$soldLabel.'</span></span>';
		}else {
			// New label
			$numberLabel = 0;
			$now = $this->getCurrentDateTime();
			$dateTimeFormat = \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT;
			$newFromDate = $product->getNewsFromDate();
			if($newFromDate) {
				$newFromDate = date($dateTimeFormat, strtotime($newFromDate));
			}
			$newToDate = $product->getNewsToDate();
			if($newToDate) {
				$newToDate = date($dateTimeFormat, strtotime($newToDate));
			}
			if($newLabel != ''){
				if(!(empty($newToDate))){
					if(!(empty($newFromDate)) && ($newFromDate < $now) && ($newToDate > $now)){
						$html.='<span class="product-label new-label"><span>'.$newLabel.'</span></span>';
						$numberLabel = 1;
					}
				}	
			}
			
			// Sale label
			$price = $product->getOrigData('price');
			$finalPrice = $product->getFinalPrice();
			$fiPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
			if($this->getStoreConfig('themesettings/catalog/sale_label_discount') == 1){
				if(($finalPrice<$price)){
					$save = $price - $finalPrice;
					$percent = round(($save * 100) / $price);
					if($numberLabel == 1){
						$html .= '<span class="product-label sale-label multiple-label"><span>-'.$percent.'%</span></span>';
					}else{
						$html .= '<span class="product-label sale-label"><span>-'.$percent.'%</span></span>';
					}
				}
			}else {

				if($saleLabel!=''){
									
					if(($finalPrice<$price) || ($fiPrice<$price)){
						//var_dump($saleLabel);
						if($numberLabel == 1){
							$html .= '<span class="product-label sale-label multiple-label"><span>'.$saleLabel.'</span></span>';
						}else{
							$html .= '<span class="product-label sale-label"><span>'.$saleLabel.'</span></span>';
						}
					}
				}
			}
		}
		return $html;
	}
	
	public function truncateString($string, $length){
		if(($length==0) || ($length=='')){
			return $string;
		}
		return $this->_filter->truncate($string, ['length' => $length]);
	}
	
	public function getMediaUrl($file = NULL){
		$url = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
		if($file != NULL){
			$url .= $file;
		}
		return $url;
	}
	
	public function getFonts() {
        $result = [
            ['css-name' => 'Lato', 'font-name' => __('Lato')],
            ['css-name' => 'Open+Sans', 'font-name' => __('Open Sans')],
            ['css-name' => 'Open+Sans+Condensed', 'font-name' => __('Open Sans Condensed')],
            ['css-name' => 'Roboto', 'font-name' => __('Roboto')],
            ['css-name' => 'Roboto+Mono', 'font-name' => __('Roboto Mono')],
            ['css-name' => 'Roboto+Slab', 'font-name' => __('Roboto Slab')],
            ['css-name' => 'Roboto+Condensed', 'font-name' => __('Roboto Condensed')],
            ['css-name' => 'Oswald', 'font-name' => __('Oswald')],
            ['css-name' => 'Source+Sans+Pro', 'font-name' => __('Source Sans Pro')],
			['css-name' => 'Source+Serif+Pro', 'font-name' => __('Source Serif Pro')],
            ['css-name' => 'PT+Sans', 'font-name' => __('PT Sans')],
            ['css-name' => 'PT+Serif', 'font-name' => __('PT Serif')],
            ['css-name' => 'PT+Sans+Narrow', 'font-name' => __('PT Sans Narrow')],
			['css-name' => 'PT+Sans+Caption', 'font-name' => __('PT Sans Caption')],
            ['css-name' => 'Droid+Serif', 'font-name' => __('Droid Serif')],
            ['css-name' => 'Josefin+Slab', 'font-name' => __('Josefin Slab')],
			['css-name' => 'Josefin+Sans', 'font-name' => __('Josefin+Sans')],
            ['css-name' => 'Montserrat', 'font-name' => __('Montserrat')],
            ['css-name' => 'Muli', 'font-name' => __('Muli')],
            ['css-name' => 'Inconsolata', 'font-name' => __('Inconsolata')],
            ['css-name' => 'Rubik', 'font-name' => __('Rubik')],
            ['css-name' => 'Archivo+Narrow', 'font-name' => __('Archivo Narrow')],
            ['css-name' => 'Mukta', 'font-name' => __('Mukta')],
            ['css-name' => 'Raleway', 'font-name' => __('Raleway')],
            ['css-name' => 'Merriweather', 'font-name' => __('Merriweather')],
			['css-name' => 'Merriweather+Sans', 'font-name' => __('Merriweather Sans')],
            ['css-name' => 'Ubuntu', 'font-name' => __('Ubuntu')],
			['css-name' => 'Ubuntu+Condensed', 'font-name' => __('Ubuntu Condensed')],
			['css-name' => 'Text+Me+One', 'font-name' => __('Text Me One')],
            ['css-name' => 'Titillium+Web', 'font-name' => __('Titillium Web')],
            ['css-name' => 'Noto+Sans', 'font-name' => __('Noto Sans')],
			['css-name' => 'Noto+Sans+TC', 'font-name' => __('Noto Sans TC')],
			['css-name' => 'Noto+Sans+SC', 'font-name' => __('Noto Sans SC')],
			['css-name' => 'Noto+Serif', 'font-name' => __('Noto Serif')],
			['css-name' => 'Noto+Sans+KR', 'font-name' => __('Noto Sans KR')],
			['css-name' => 'Noto+Sans+JP', 'font-name' => __('Noto Sans JP')],
			['css-name' => 'Nunito', 'font-name' => __('Nunito')],
			['css-name' => 'Nunito+Sans', 'font-name' => __('Nunito Sans')],
			['css-name' => 'Varela+Round', 'font-name' => __('Varela Round')],
			['css-name' => 'Bai+Jamjuree', 'font-name' => __('Bai Jamjuree')],
            ['css-name' => 'Lora', 'font-name' => __('Lora')],
            ['css-name' => 'Playfair+Display', 'font-name' => __('Playfair Display')],
            ['css-name' => 'Yanone+Kaffeesatz', 'font-name' => __('Yanone Kaffeesatz')],
            ['css-name' => 'Fira+Sans', 'font-name' => __('Fira Sans')],
			['css-name' => 'Fira+Sans+Condensed', 'font-name' => __('Fira Sans Condensed')],
            ['css-name' => 'Bree+Serif', 'font-name' => __('Bree Serif')],
            ['css-name' => 'Sedgwick+Ave', 'font-name' => __('Sedgwick Ave')],
            ['css-name' => 'Vollkorn', 'font-name' => __('Vollkorn')],
            ['css-name' => 'Arimo', 'font-name' => __('Arimo')],
            ['css-name' => 'Asap', 'font-name' => __('Asap')],
            ['css-name' => 'Alegreya', 'font-name' => __('Alegreya')],
            ['css-name' => 'Abel', 'font-name' => __('Abel')],
			['css-name' => 'Exo', 'font-name' => __('Exo')],
            ['css-name' => 'Exo+2', 'font-name' => __('Exo 2')],
            ['css-name' => 'Black+Han+Sans', 'font-name' => __('Black Han Sans')],
            ['css-name' => 'Libre+Baskerville', 'font-name' => __('Libre Baskerville')],
			['css-name' => 'Libre+Franklin', 'font-name' => __('Libre Franklin')],
			['css-name' => 'Karla', 'font-name' => __('Karla')],
            ['css-name' => 'Poppins', 'font-name' => __('Poppins')],
            ['css-name' => 'IBM+Plex+Mono', 'font-name' => __('IBM Plex Mono')],
            ['css-name' => 'Catamaran', 'font-name' => __('Catamaran')],
            ['css-name' => 'Cairo', 'font-name' => __('Cairo')],
            ['css-name' => 'Signika', 'font-name' => __('Signika')],
            ['css-name' => 'Heebo', 'font-name' => __('Heebo')],
            ['css-name' => 'Cuprum', 'font-name' => __('Cuprum')],
            ['css-name' => 'Aref+Ruqaa', 'font-name' => __('Aref Ruqaa')],
            ['css-name' => 'Dosis', 'font-name' => __('Dosis')],
            ['css-name' => 'Encode+Sans+Condensed', 'font-name' => __('Encode Sans Condensed')],
            ['css-name' => 'Crimson+Text', 'font-name' => __('Crimson Text')],
            ['css-name' => 'Quicksand', 'font-name' => __('Quicksand')],
            ['css-name' => 'Hind', 'font-name' => __('Hind')],
            ['css-name' => 'Cabin', 'font-name' => __('Cabin')],
            ['css-name' => 'Nanum+Gothic', 'font-name' => __('Nanum+Gothic')],
            ['css-name' => 'Bitter', 'font-name' => __('Bitter')],
            ['css-name' => 'Oxygen', 'font-name' => __('Oxygen')],
            ['css-name' => 'Anton', 'font-name' => __('Anton')],
            ['css-name' => 'Arvo', 'font-name' => __('Arvo')],
            ['css-name' => 'Acme', 'font-name' => __('Acme')],
            ['css-name' => 'Fjalla+One', 'font-name' => __('Fjalla One')],
            
            
            ['css-name' => 'Work+Sans', 'font-name' => __('Work Sans')]
        ];
		
		$customFont = $this->getCustomFonts();

		if(count($customFont)>0){
			foreach($customFont as $font=>$file){
				$result[] = ['css-name'=>$font, 'font-name' => $font];
			}
		}
		
		return $result;
    }
	
	public function getCustomFonts(){
		$result = [];
		$customFont = ['first', 'second', 'third', 'fourth', 'fifth'];
		
		foreach($customFont as $font){
			$fontName = $this->getStoreConfig('themestyle/custom_font/font_name_'.$font);
			$fontDir = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . \MGS\ThemeSettings\Model\Config\Backend\Font::UPLOAD_DIR.'/';
			if($fontName!=''){
				$ttfFile = $this->getStoreConfig('themestyle/custom_font/ttf_file_'.$font);
				$eotFile = $this->getStoreConfig('themestyle/custom_font/eot_file_'.$font);
				$woffFile = $this->getStoreConfig('themestyle/custom_font/woff_file_'.$font);
				$woffTwoFile = $this->getStoreConfig('themestyle/custom_font/woff_two_file_'.$font);
				$svgFile = $this->getStoreConfig('themestyle/custom_font/svg_file_'.$font);

				if (($ttfFile != '' && $eotFile != '') || ($woffTwoFile!='')) {
					if($ttfFile!=''){
						$result[$fontName]['ttf'] = $fontDir.$ttfFile;
					}
					
					if($eotFile!=''){
						$result[$fontName]['eot'] = $fontDir.$eotFile;
					}
					
					if($woffFile!=''){
						$result[$fontName]['woff'] = $fontDir.$woffFile;
					}
					
					if($woffTwoFile!=''){
						$result[$fontName]['woff2'] = $fontDir.$woffTwoFile;
					}
					
					if($svgFile!=''){
						$result[$fontName]['svg'] = $fontDir.$svgFile;
					}
				}

			}
		}
		return $result;
	}
	
	public function getFullActionName() {
		$request = $this->_objectManager->get('\Magento\Framework\App\Request\Http');
		return $request->getFullActionName();
	}
	
	public function isCategoryPage(){

		if ($this->_request->getFullActionName() == 'catalog_category_view') {
			return true;
		}
		return false;
	}
	public function getCurrentCategory(){
		$id = $this->_request->getParam('id');
		$this->_currentCategory = $this->getModel('Magento\Catalog\Model\Category')->load($id);
		return $this->_currentCategory;
	}
	public function getPageTitleBackground(){
		$img = '';
		
		if($this->getStoreConfig('themesettings/page_title/background_image')){
			$img = $this->getMediaUrl() . 'bg_pagetitle/' . $this->getStoreConfig('themesettings/page_title/background_image');
		}
        
        if($this->isCategoryPage() && $this->getStoreConfig('themesettings/page_title/category_image')){
			
            $category = $this->getCurrentCategory();
            $imgName = $category->getImageUrl();
            if($imgName){
                $img = $imgName;
            }
        }
        
        return $img;
	}
	
	public function getRotateImages($productId){
		$dir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('wysiwyg/360/'.$productId);
		
		$result = [];
		$files = [];
		if(is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while ($files[] = readdir($dh));
				sort($files);
				foreach ($files as $file){
					$file_parts = pathinfo($dir . $file);
					if (isset($file_parts['extension']) && (($file_parts['extension'] == 'jpg') || ($file_parts['extension'] == 'png'))) {
                        $result[] = $this->getMediaUrl().'wysiwyg/360/'.$productId.'/'.$file;
                    }
				}
                closedir($dh);
            }
        }
		return $result;
	}
	
	public function getImageLazyLoad($ratio = NULL){
		if($ratio == NULL){
			$ratio = 1;
		}
		return $this->getMediaUrl() . 'mgs/fbuilder/images/blank'.$ratio.'.png';
	}
	
	public function getThemeSettingStyle($storeId){
		$html = '';
		
		$customFont = $this->getCustomFonts();
		
		$googleFont = $fontCss = '';
		
		$defaultFont = $this->getStoreConfig('themestyle/font/default_font', $storeId);
		
		if($defaultFont!=''){
			if(!isset($customFont[$defaultFont])){
				$googleFont .="
				@import url('//fonts.googleapis.com/css?family=".str_replace(' ','+',$defaultFont);
				$defaultFontWeightToImport = $this->getStoreConfig('themestyle/font/default_font_weight_import', $storeId);
				
				if($defaultFontWeightToImport!=''){
					$googleFont .= ":".$defaultFontWeightToImport;
				}
				$googleFont .="');
				";
				$fontCss .= "html{font-family:'".str_replace("+"," ",$defaultFont)."', 'Open Sans', 'Helvetica Neue';";
				$defaultFontWeight = $this->getStoreConfig('themestyle/font/default_font_weight', $storeId);
				if($defaultFontWeight!=''){
					$fontCss .= "font-weight:".$defaultFontWeight.";";
				}
				
			}else{
				$fontCss .= "html{font-family:'".str_replace("+"," ",$defaultFont)."', 'Open Sans', 'Helvetica Neue';font-weight:normal; font-style:normal;";
			}
			
			
			$fontCss .= "}";
			
		}
		
		$defaultFontSize = $this->getStoreConfig('themestyle/font/default_font_size', $storeId);
		if($defaultFontSize!=''){
			$fontCss .= "html{font-size:".$defaultFontSize."px;}";
		}
		
		$arrFont = ['heading_one'=>'h1', 'heading_two'=>'h2', 'heading_three'=>'h3', 'heading_four'=>'h4', 'heading_five'=>'h5', 'heading_six'=>'h6', 'price'=>'.price-box .price', 'menu'=>'#mainMenu a.level0, nav.navigation a.level-top', 'button'=>'button.action', 'custom'=>$this->getStoreConfig('themestyle/font/elements', $storeId)];
		
		foreach($arrFont as $key=>$class){
			$configFontName = $this->getStoreConfig('themestyle/font/'.$key.'_font', $storeId);
			
			if($configFontName!=''){
				if(!isset($customFont[$configFontName])){
					$googleFont .="
					@import url('//fonts.googleapis.com/css?family=".str_replace(' ','+',$configFontName);
					$fontWeightToImport = $this->getStoreConfig('themestyle/font/'.$key.'_font_weight', $storeId);
					
					if($fontWeightToImport!=''){
						$googleFont .= ":".$fontWeightToImport;
					}
					$googleFont .="');
					";
					$fontCss .= $class."{font-family:'".str_replace("+"," ",$configFontName)."', 'Open Sans', 'Helvetica Neue';";
					
					if($fontWeightToImport!=''){
						$fontCss .= "font-weight:".$fontWeightToImport.";";
					}
					
				}else{
					$fontCss .= $class."{font-family:'".str_replace("+"," ",$configFontName)."', 'Open Sans', 'Helvetica Neue'; font-weight:normal; font-style:normal;";
				}
				$fontCss .= "}";
			}
			$fontSize = $this->getStoreConfig('themestyle/font/'.$key.'_font_size', $storeId);
			if($fontSize!=''){
				$fontCss .= $class."{font-size:".$fontSize."}";
			}
		}
		
		$html .= $googleFont;
		
		if(count($customFont)>0){
			foreach($customFont as $fontName=>$files){
				$embedFontString = '';

				if(isset($files['eot'])){
					$embedFontString .= 'src: url("'.$files['eot'].'");';
				}
				$embedFontString .= 'src:';
				if(isset($files['eot'])){
					$embedFontString .= 'url("'.$files['eot'].'?#iefix") format("embedded-opentype"),';
				}
				if(isset($files['woff'])){
					$embedFontString .= 'url("'.$files['woff'].'") format("woff"),';
				}
				if(isset($files['woff'])){
					$embedFontString .= 'url("'.$files['woff'].'") format("woff"),';
				}
				if(isset($files['ttf'])){
					$embedFontString .= 'url("'.$files['ttf'].'")  format("truetype"),';
				}
				if(isset($files['woff2'])){
					$embedFontString .= 'url("'.$files['woff2'].'") format("woff2"),';
				}
				if(isset($files['svg'])){
					$embedFontString .= 'url("'.$files['svg'].'#svgFontName") format("svg")';
				}
				if(substr($embedFontString, -1)==','){
					$embedFontString = substr($embedFontString, 0, -1);
				}

				$html .= "@font-face {
					font-family: '".$fontName."';
					".$embedFontString.";	 
					font-weight: normal;
					font-style: normal;
				}";
			}
		}
		
		$html .= $fontCss;
		
		
		/* Main Background */
		$html .= 'body{';
		if($this->getStoreConfig('themesettings/general/custom_background', $storeId)){
			$backgroundColor = $this->getStoreConfig('themesettings/general/background_color', $storeId);
			$backgroundImage = $this->getStoreConfig('themesettings/general/background_image', $storeId);
			
			if($backgroundColor!=''){
				$html .= 'background-color:'.$backgroundColor.';';
			}
			
			if($backgroundImage!=''){
				$backgroundImageUrl = $this->getMediaUrl('mgs/background/'.$backgroundImage);

				$html .= 'background-image:url('.$backgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/general/background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/general/background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/general/background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/general/background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
		}
		$html .= '}';
		
		/* Header */
		$html .= 'header.page-header{';
		if($this->getStoreConfig('themesettings/header/custom_header_background', $storeId)){
			$backgroundColor = $this->getStoreConfig('themesettings/header/background_color', $storeId);
			$backgroundImage = $this->getStoreConfig('themesettings/header/background_image', $storeId);
			
			if($backgroundColor!=''){
				$html .= 'background-color:'.$backgroundColor.';';
			}
			
			if($backgroundImage!=''){
				$backgroundImageUrl = $this->getMediaUrl('mgs/background/'.$backgroundImage);

				$html .= 'background-image:url('.$backgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/header/background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/header/background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/header/background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/header/background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
		}
		if($this->getStoreConfig('themesettings/header/custom_header_border', $storeId)){
			$borderTopSize = $this->getStoreConfig('themesettings/header/border_top_size', $storeId);
			$borderTopColor = $this->getStoreConfig('themesettings/header/border_top_color', $storeId);
			if($borderTopSize !='' && $borderTopColor!=''){
				$html .= 'border-top:'.$borderTopSize.'px solid '.$borderTopColor.';';
			}
			
			$borderBottomSize = $this->getStoreConfig('themesettings/header/border_bottom_size', $storeId);
			$borderBottomColor = $this->getStoreConfig('themesettings/header/border_bottom_color', $storeId);
			if($borderBottomSize !='' && $borderBottomColor!=''){
				$html .= 'border-bottom:'.$borderBottomSize.'px solid '.$borderBottomColor.';';
			}
		}
		
		$html .= '}';
		
		/* Top Header */
		if($this->getStoreConfig('themesettings/header/display_top_header', $storeId)){
			$html .= 'header .top-header{';
			
			/* Top Header: Background */
			$topHeaderBackgroundColor = $this->getStoreConfig('themesettings/header/top_header_background_color', $storeId);
			$topHeaderBackgroundImage = $this->getStoreConfig('themesettings/header/top_header_background_image', $storeId);
			if($topHeaderBackgroundColor!=''){
				$html .= 'background-color:'.$topHeaderBackgroundColor.';';
			}
			if($topHeaderBackgroundImage!=''){
				$topHeaderBackgroundImageUrl = $this->getMediaUrl('mgs/background/'.$topHeaderBackgroundImage);

				$html .= 'background-image:url('.$topHeaderBackgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/header/top_header_background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/header/top_header_background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/header/top_header_background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/header/top_header_background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
			
			/* Top Header: Text */
			$topHeaderTextColor = $this->getStoreConfig('themesettings/header/top_header_text_color', $storeId);
			if($topHeaderTextColor!=''){
				$html .= 'color:'.$topHeaderTextColor.';';
			}
			$html .= '}';
			
			/* Top Header: Link */
			$topHeaderLinkColor = $this->getStoreConfig('themesettings/header/top_header_link_color', $storeId);
			$topHeaderLinkHoverColor = $this->getStoreConfig('themesettings/header/top_header_link_hover_color', $storeId);
			if($topHeaderLinkColor!=''){
				$html .= 'header .top-header a{color:'.$topHeaderLinkColor.';}';
			}
			if($topHeaderLinkHoverColor!=''){
				$html .= 'header .top-header a:hover{color:'.$topHeaderLinkHoverColor.';}';
			}
		}
		
		/* Middle Header */
		if($this->getStoreConfig('themesettings/header/custom_middle_header', $storeId)){
			$html .= 'header .middle-header{';
			
			/* Middle Header: Background */
			$middleHeaderBackgroundColor = $this->getStoreConfig('themesettings/header/middle_header_background_color', $storeId);
			$middleHeaderBackgroundImage = $this->getStoreConfig('themesettings/header/middle_header_background_image', $storeId);
			if($middleHeaderBackgroundColor!=''){
				$html .= 'background-color:'.$middleHeaderBackgroundColor.';';
			}
			if($middleHeaderBackgroundImage!=''){
				$middleHeaderBackgroundImageUrl = $this->getMediaUrl('mgs/background/'.$middleHeaderBackgroundImage);

				$html .= 'background-image:url('.$middleHeaderBackgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/header/middle_header_background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/header/middle_header_background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/header/middle_header_background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/header/middle_header_background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
			
			/* Middle Header: Text */
			$middleHeaderTextColor = $this->getStoreConfig('themesettings/header/middle_header_text_color', $storeId);
			if($middleHeaderTextColor!=''){
				$html .= 'color:'.$middleHeaderTextColor.';';
			}
			$html .= '}';
			
			/* Middle Header: Link */
			$middleHeaderLinkColor = $this->getStoreConfig('themesettings/header/middle_header_link_color', $storeId);
			$middleHeaderLinkHoverColor = $this->getStoreConfig('themesettings/header/middle_header_link_hover_color', $storeId);
			if($middleHeaderLinkColor!=''){
				$html .= 'header .middle-header a{color:'.$middleHeaderLinkColor.';}';
			}
			if($middleHeaderLinkHoverColor!=''){
				$html .= 'header .middle-header a:hover{color:'.$middleHeaderLinkHoverColor.';}';
			}
			
			/* Middle Header: Icon */
			$middleHeaderIconColor = $this->getStoreConfig('themesettings/header/middle_header_icon_color', $storeId);
			$middleHeaderIconHoverColor = $this->getStoreConfig('themesettings/header/middle_header_icon_hover_color', $storeId);
			if($middleHeaderIconColor!=''){
				$html .= 'header .middle-header .theme-header-icon{color:'.$middleHeaderIconColor.';}';
			}
			if($middleHeaderIconHoverColor!=''){
				$html .= 'header .middle-header .theme-header-icon:hover,header .middle-header .block-search.active .theme-header-icon, header .middle-header .setting-site.active .theme-header-icon,header .middle-header .minicart-wrapper.active .theme-header-icon,header .middle-header .header-top-links.active .theme-header-icon{color:'.$middleHeaderIconHoverColor.';}';
			}
		}
		
		/* Bottom Header */
		if($this->getStoreConfig('themesettings/header/custom_bottom_header', $storeId)){
			$html .= 'header .bottom-header{';
			
			/* Bottom Header: Background */
			$bottomHeaderBackgroundColor = $this->getStoreConfig('themesettings/header/bottom_header_background_color', $storeId);
			$bottomHeaderBackgroundImage = $this->getStoreConfig('themesettings/header/bottom_header_background_image', $storeId);
			if($bottomHeaderBackgroundColor!=''){
				$html .= 'background-color:'.$bottomHeaderBackgroundColor.';';
			}
			if($bottomHeaderBackgroundImage!=''){
				$bottomHeaderBackgroundImageUrl = $this->getMediaUrl('mgs/background/'.$bottomHeaderBackgroundImage);

				$html .= 'background-image:url('.$bottomHeaderBackgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/header/bottom_header_background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/header/bottom_header_background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/header/bottom_header_background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/header/bottom_header_background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
			
			/* Bottom Header: Text */
			$bottomHeaderTextColor = $this->getStoreConfig('themesettings/header/bottom_header_text_color', $storeId);
			if($bottomHeaderTextColor!=''){
				$html .= 'color:'.$bottomHeaderTextColor.';';
			}
			$html .= '}';
			
			/* Bottom Header: Link */
			$bottomHeaderLinkColor = $this->getStoreConfig('themesettings/header/bottom_header_link_color', $storeId);
			$bottomHeaderLinkHoverColor = $this->getStoreConfig('themesettings/header/bottom_header_link_hover_color', $storeId);
			if($bottomHeaderLinkColor!=''){
				$html .= 'header .bottom-header a{color:'.$bottomHeaderLinkColor.';}';
			}
			if($bottomHeaderLinkHoverColor!=''){
				$html .= 'header .bottom-header a:hover{color:'.$bottomHeaderLinkHoverColor.';}';
			}
			
			/* Bottom Header: Icon */
			$bottomHeaderIconColor = $this->getStoreConfig('themesettings/header/bottom_header_icon_color', $storeId);
			$bottomHeaderIconHoverColor = $this->getStoreConfig('themesettings/header/bottom_header_icon_hover_color', $storeId);
			if($bottomHeaderIconColor!=''){
				$html .= 'header .bottom-header .theme-header-icon{color:'.$bottomHeaderIconColor.';}';
			}
			if($bottomHeaderIconHoverColor!=''){
				$html .= 'header .bottom-header .theme-header-icon:hover,header .bottom-header .block-search.active .theme-header-icon, header .bottom-header .setting-site.active .theme-header-icon,header .bottom-header .minicart-wrapper.active .theme-header-icon,header .bottom-header .header-top-links.active .theme-header-icon{color:'.$bottomHeaderIconHoverColor.';}';
			}
		}
		
		/*Mini Cart*/
		if($this->getStoreConfig('themesettings/header/mini_cart_custom', $storeId)){
			/*Mini Cart Icon*/
			$miniCartIconColor = $this->getStoreConfig('themesettings/header/cart_icon_color', $storeId);
			$miniCartIconHoverColor = $this->getStoreConfig('themesettings/header/cart_icon_hover_color', $storeId);
			
			if($miniCartIconColor!=''){
				$html .= 'header.page-header .minicart-wrapper a.action.showcart::before{color:'.$miniCartIconColor.';}';
			}
			if($miniCartIconHoverColor!=''){
				$html .= 'header.page-header .minicart-wrapper a.action.showcart:hover::before{color:'.$miniCartIconHoverColor.';}';
			}
			
			/*Mini Cart Number Text*/
			$miniCartNumberColor = $this->getStoreConfig('themesettings/header/cart_number_color', $storeId);
			if($miniCartNumberColor!=''){
				$html .= 'header.page-header .minicart-wrapper a.action.showcart .counter.qty{color:'.$miniCartNumberColor.';}';
			}
			
			/*Mini Cart Number Background*/
			$miniCartNumberBackground = $this->getStoreConfig('themesettings/header/cart_number_background_color', $storeId);
			if($miniCartNumberBackground!=''){
				$html .= 'header.page-header .minicart-wrapper a.action.showcart .counter.qty{background:'.$miniCartNumberBackground.';}';
			}
			
			/*Mini Cart Text Color*/
			$miniCartTextColor = $this->getStoreConfig('themesettings/header/cart_text_color', $storeId);
			if($miniCartTextColor!=''){
				$html .= 'header.page-header .minicart-wrapper .block-minicart .subtitle.empty span, .minicart-wrapper .block-content > .actions > .subtotal > span.label, header.page-header .minicart-items .product-item-details .product-item-name a, header.page-header .minicart-items .product .actions a:before, header.page-header .minicart-items .product-item-pricing .details-qty .label{color:'.$miniCartTextColor.';}';
			}
			
			/*Mini Cart Heading Color*/
			$miniCartHeadingColor = $this->getStoreConfig('themesettings/header/cart_heading_color', $storeId);
			if($miniCartHeadingColor!=''){
				$html .= 'header.page-header .minicart-wrapper .top-cart-title{color:'.$miniCartHeadingColor.';}';
			}
			
			/*Mini Cart Background Color*/
			$miniCartBackgroundColor = $this->getStoreConfig('themesettings/header/cart_background_color', $storeId);
			if($miniCartBackgroundColor!=''){
				$html .= 'header.page-header .minicart-wrapper.active .block-minicart{background:'.$miniCartBackgroundColor.';}';
			}
			
			/*Mini Cart Close Icon Color*/
			$miniCartCloseColor = $this->getStoreConfig('themesettings/header/cart_close_icon_color', $storeId);
			if($miniCartCloseColor!=''){
				$html .= 'header.page-header .minicart-wrapper .block-content .action.close, header.page-header .minicart-wrapper .block-content .action.close:before{color:'.$miniCartCloseColor.';}';
			}
			
			/*Mini Cart Divide Border Color*/
			$miniCartDivideBorderColor = $this->getStoreConfig('themesettings/header/cart_divide_border_color', $storeId);
			if($miniCartDivideBorderColor!=''){
				$html .= 'header.page-header .minicart-wrapper .top-cart-title, header.page-header .minicart-wrapper .minicart-items-wrapper .product-item+.product-item{border-color:'.$miniCartDivideBorderColor.';}';
			}
		}
		
		/*Search*/
		if($this->getStoreConfig('themesettings/header/search_custom', $storeId)){
			/*Text Color*/
			$html .= 'header.page-header .block-search .block-content input{';
			$searchTextColor = $this->getStoreConfig('themesettings/header/search_text_color', $storeId);
			if($searchTextColor!=''){
				$html .= 'color:'.$searchTextColor.';';
			}
			
			/*Text Color*/
			$searchBackgroundColor = $this->getStoreConfig('themesettings/header/search_background_color', $storeId);
			if($searchBackgroundColor!=''){
				$html .= 'background-color:'.$searchBackgroundColor.';';
			}
			
			/*Text Color*/
			$searchBorderColor = $this->getStoreConfig('themesettings/header/search_border_color', $storeId);
			if($searchBorderColor!=''){
				$html .= 'border-color:'.$searchBorderColor.';';
			}
			
			$html .= '}';
		}
		
		/*Menu*/
		if($this->getStoreConfig('themesettings/header/menu_custom', $storeId)){
			/* Level 1 */
			$menuLevel1Color = $this->getStoreConfig('themesettings/header/menu_main_color', $storeId);
			if($menuLevel1Color!=''){
				$html .= '.navigation .level0 > .level-top, .navigation .level0 a.level0, .header-area:not(.push-menu):not(.semi-push-menu) .horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0>a.level0{color:'.$menuLevel1Color.'}';
				$html .= '.header-area:not(.push-menu):not(.semi-push-menu) .horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0>a.level0:after{background:'.$menuLevel1Color.'}';
			}
			
			/* Level 1 hover */
			$menuLevel1HoverColor = $this->getStoreConfig('themesettings/header/menu_main_hover_color', $storeId);
			if($menuLevel1HoverColor!=''){
				$html .= '.navigation .level0 > .level-top:hover, .navigation .level0 > .level-top:active, .navigation .level0.active > .level-top, .navigation .level0 a.level0:hover, .header-area:not(.push-menu):not(.semi-push-menu) .horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0>a.level0:hover{color:'.$menuLevel1HoverColor.'}';
				$html .= '.header-area:not(.push-menu):not(.semi-push-menu) .horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0>a.level0:hover:after{background:'.$menuLevel1HoverColor.'}';
			}
			
			/* Dropdown Heading color */
			$menuDropdownHeadingColor = $this->getStoreConfig('themesettings/header/menu_dropdown_heading_color', $storeId);
			if($menuDropdownHeadingColor!=''){
				$html .= '.navigation .level0 .dropdown-mega-menu h1, .navigation .level0 .dropdown-mega-menu h2, .navigation .level0 .dropdown-mega-menu h3, .navigation .level0 .dropdown-mega-menu h4, .navigation .level0 .dropdown-mega-menu h5, .navigation .level0 .dropdown-mega-menu h6, .navigation .level0 .dropdown-mega-menu .mega-menu-sub-title{color:'.$menuDropdownHeadingColor.'}';
			}
			
			/* Dropdown Link color */
			$menuDropdownLinkColor = $this->getStoreConfig('themesettings/header/menu_dropdown_link_color', $storeId);
			if($menuDropdownLinkColor!=''){
				$html .= '.navigation .level0 .submenu a, .navigation .level0 .dropdown-mega-menu .sub-menu a,.dropdown-mega-menu .level1 a{color:'.$menuDropdownLinkColor.'}';
			}
			
			/* Dropdown Link hover color */
			$menuDropdownLinkHoverColor = $this->getStoreConfig('themesettings/header/menu_dropdown_link_hover_color', $storeId);
			if($menuDropdownLinkHoverColor!=''){
				$html .= '.navigation .level0 .submenu a:hover,.navigation .level0 .submenu .active a, .navigation .level0 .dropdown-mega-menu .sub-menu a:hover, .dropdown-mega-menu .level1 a:hover{color:'.$menuDropdownLinkHoverColor.'}';
			}
			
			/* Dropdown background color */
			$menuDropdownBackgroundColor = $this->getStoreConfig('themesettings/header/menu_dropdown_background', $storeId);
			$menuDropdownOpacity = $this->getStoreConfig('themesettings/header/menu_dropdown_opacity', $storeId);
			if($menuDropdownOpacity==''){
				$menuDropdownOpacity = 1;
			}
			
			list($r, $g, $b) = sscanf($menuDropdownBackgroundColor, "#%02x%02x%02x");

			if($menuDropdownBackgroundColor!=''){
				$html .= '.navigation .level0 .submenu, .navigation .level0 .dropdown-mega-menu, .header-area:not(.push-menu):not(.semi-push-menu) .horizontal-menu .mgs-megamenu--main .nav-main-menu li.level0:not(.menu-1columns)._hover .dropdown-mega-menu, .header-area .horizontal-menu .mgs-megamenu--main .nav-main-menu .mega-menu-item .dropdown-mega-menu{background-color: rgba('.$r.','.$g.','.$b.','.$menuDropdownOpacity.') !important;}';
			}
			
			/* Dropdown border color */
			$menuDropdownBorderColor = $this->getStoreConfig('themesettings/header/menu_dropdown_divide_color', $storeId);
			list($r, $g, $b) = sscanf($menuDropdownBorderColor, "#%02x%02x%02x");
			if($menuDropdownBorderColor!=''){
				$html .= '.navigation .level0 .submenu, .navigation .level0 .dropdown-mega-menu .sub-menu li.level2, .mega-menu-content hr{border-color:rgba('.$r.','.$g.','.$b.','.$menuDropdownOpacity.');}';
			}
		}
		
		/* My Account */
		if($this->getStoreConfig('themesettings/header/my_account_custom', $storeId)){
			/* Background color */
			$myAccountBackgroundColor = $this->getStoreConfig('themesettings/header/my_account_background', $storeId);
			if($myAccountBackgroundColor!=''){
				$html .= '.header-top-links .login-form{background-color:'.$myAccountBackgroundColor.'}';
			}
			
			/* Text color */
			$myAccountTextColor = $this->getStoreConfig('themesettings/header/my_account_text_color', $storeId);
			if($myAccountTextColor!=''){
				$html .= '.header-top-links .login-form{color:'.$myAccountTextColor.'}';
			}
			
			/* Link color */
			$myAccountLinkColor = $this->getStoreConfig('themesettings/header/my_account_link_color', $storeId);
			if($myAccountLinkColor!=''){
				$html .= '.header-top-links .login-form a{color:'.$myAccountLinkColor.'}';
			}
			
			/* Link hover color */
			$myAccountLinkHoverColor = $this->getStoreConfig('themesettings/header/my_account_link_hover_color', $storeId);
			if($myAccountLinkHoverColor!=''){
				$html .= '.header-top-links .login-form a:hover{color:'.$myAccountLinkHoverColor.'}';
			}
			
			/* Heading color */
			$myAccountHeadingColor = $this->getStoreConfig('themesettings/header/my_account_heading_color', $storeId);
			if($myAccountHeadingColor!=''){
				$html .= '.header-top-links .login-form .block-title{color:'.$myAccountHeadingColor.'}';
			}
			
			/* Button Background color */
			$html .= '.header-top-links .login-form button{';
			$myAccountButtonBackgroundColor = $this->getStoreConfig('themesettings/header/my_account_button_background', $storeId);
			
			if($myAccountButtonBackgroundColor!=''){
				$html .= 'background-color:'.$myAccountButtonBackgroundColor.';';
			}
			
			$myAccountButtonTextColor = $this->getStoreConfig('themesettings/header/my_account_button_text_color', $storeId);
			if($myAccountButtonTextColor!=''){
				$html .= 'color:'.$myAccountButtonTextColor.';';
			}
			
			$myAccountButtonBorderColor = $this->getStoreConfig('themesettings/header/my_account_button_border', $storeId);
			if($myAccountButtonBorderColor!=''){
				$html .= 'border-color:'.$myAccountButtonBorderColor.';';
			}
			$html .= '}';
			
			/* Button Background color */
			$myAccountButtonBackgroundHoverColor = $this->getStoreConfig('themesettings/header/my_account_button_hover_background', $storeId);
			$html .= '.header-top-links .login-form button:hover{';
			if($myAccountButtonBackgroundHoverColor!=''){
				$html .= 'background-color:'.$myAccountButtonBackgroundHoverColor.';';
			}
			$myAccountButtonTextHoverColor = $this->getStoreConfig('themesettings/header/my_account_button_text_hover_color', $storeId);
			if($myAccountButtonTextHoverColor!=''){
				$html .= 'color:'.$myAccountButtonTextHoverColor.';';
			}
			
			$myAccountButtonBorderHoverColor = $this->getStoreConfig('themesettings/header/my_account_button_border_hover', $storeId);
			if($myAccountButtonBorderHoverColor!=''){
				$html .= 'border-color:'.$myAccountButtonBorderHoverColor.';';
			}
			
			$html .= '}';
			
			/* Input color */
			$myAccountInputColor = $this->getStoreConfig('themesettings/header/my_account_input_text_color', $storeId);
			if($myAccountInputColor!=''){
				$html .= '.header-top-links .login-form input.input-text{color:'.$myAccountInputColor.'}';
			}
			
			/* Input background */
			$myAccountInputBackground = $this->getStoreConfig('themesettings/header/my_account_input_background', $storeId);
			if($myAccountInputBackground!=''){
				$html .= '.header-top-links .login-form input.input-text{background-color:'.$myAccountInputBackground.'}';
			}
			
			/* Input border color */
			$myAccountInputBorderColor = $this->getStoreConfig('themesettings/header/my_account_input_border', $storeId);
			if($myAccountInputBorderColor!=''){
				$html .= '.header-top-links .login-form input.input-text{border-color:'.$myAccountInputBorderColor.'}';
			}
			
		}
		
		/* Dropdown */
		if($this->getStoreConfig('themesettings/header/dropdown_custom', $storeId)){
			/* Dropdown background color */
			$dropdownBackgroundColor = $this->getStoreConfig('themesettings/header/dropdown_background', $storeId);
			$dropdownOpacity = $this->getStoreConfig('themesettings/header/dropdown_opacity', $storeId);
			if($dropdownOpacity==''){
				$dropdownOpacity = 1;
			}
			
			list($r, $g, $b) = sscanf($dropdownBackgroundColor, "#%02x%02x%02x");

			if($dropdownBackgroundColor!=''){
				$html .= '.page-header .switcher .options ul.dropdown, header.page-header .setting-site.active .setting-site-content, header.page-header .setting-site .setting-site-content .actions-close{background-color: rgba('.$r.','.$g.','.$b.','.$dropdownOpacity.'); border:none}';
				$html .= '.page-header .switcher .options ul.dropdown li:hover{background:none}';
				$html .= '.page-header .switcher .options ul.dropdown::before{border-color: transparent transparent rgba('.$r.','.$g.','.$b.','.$dropdownOpacity.') transparent;}';
				$html .= '.page-header .switcher .options ul.dropdown::after{border-color: transparent transparent transparent transparent;}';
			}
			
			/* Border color */
			$dropdownBorderColor = $this->getStoreConfig('themesettings/header/dropdown_divide_color', $storeId);
			list($r, $g, $b) = sscanf($dropdownBorderColor, "#%02x%02x%02x");
			if($dropdownBorderColor!=''){
				$html .= '.page-header .switcher .options ul.dropdown li, header.page-header .setting-site .customer-web-config .switcher .switcher-dropdown li{border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:rgba('.$r.','.$g.','.$b.','.$dropdownOpacity.')}';
				$html .= '.page-header .switcher .options ul.dropdown li:last-child, header.page-header .setting-site .customer-web-config .switcher .switcher-dropdown li:last-child{border:none}';
			}
			
			/* Link color */
			$dropdownLinkColor = $this->getStoreConfig('themesettings/header/dropdown_link_color', $storeId);
			if($dropdownLinkColor!=''){
				$html .= '.page-header .switcher .options ul.dropdown li a, header.page-header .setting-site .customer-web-config .switcher .switcher-dropdown li a{color:'.$dropdownLinkColor.'}';
			}
			
			/* Link hover color */
			$dropdownLinkHoverColor = $this->getStoreConfig('themesettings/header/dropdown_link_hover_color', $storeId);
			if($dropdownLinkHoverColor!=''){
				$html .= '.page-header .switcher .options ul.dropdown li a:hover, header.page-header .setting-site .customer-web-config .switcher .switcher-dropdown li a:hover{color:'.$dropdownLinkHoverColor.'}';
			}
		}
		
		/* Main Content */
		if($this->getStoreConfig('themesettings/main/custom_text_link', $storeId)){
			/* Text color */
			$mainTextColor = $this->getStoreConfig('themesettings/main/text_color', $storeId);
			if($mainTextColor!=''){
				$html .= 'body{color:'.$mainTextColor.'}';
			}
			
			/* Link color */
			$mainLinkColor = $this->getStoreConfig('themesettings/main/link_color', $storeId);
			if($mainLinkColor!=''){
				$html .= 'a:visited, a{color:'.$mainLinkColor.'}';
			}
			
			/* Link hover color */
			$mainLinkHoverColor = $this->getStoreConfig('themesettings/main/link_hover_color', $storeId);
			if($mainLinkHoverColor!=''){
				$html .= 'a:hover, a:focus{color:'.$mainLinkHoverColor.'}';
			}
			
			/* Price color */
			$mainPriceColor = $this->getStoreConfig('themesettings/main/price_color', $storeId);
			if($mainPriceColor!=''){
				$html .= '.price-box .price, .price{color:'.$mainPriceColor.'}';
			}
			
			/* Old price color */
			$mainOldPriceColor = $this->getStoreConfig('themesettings/main/old_price_color', $storeId);
			if($mainOldPriceColor!=''){
				$html .= '.price-box .old-price .price{color:'.$mainOldPriceColor.'}';
			}
			
			/* Special price color */
			$mainSpecialPriceColor = $this->getStoreConfig('themesettings/main/special_price_color', $storeId);
			if($mainSpecialPriceColor!=''){
				$html .= '.price-box .special-price .price{color:'.$mainSpecialPriceColor.'}';
			}
		}
		
		/* Primary Button */
		if($this->getStoreConfig('themesettings/main/custom_primary_button', $storeId)){
			/* Text color */
			$primaryButtonTextColor = $this->getStoreConfig('themesettings/main/primary_button_text_color', $storeId);
			if($primaryButtonTextColor!=''){
				$html .= 'button.primary{color:'.$primaryButtonTextColor.'}';
			}
			/* Text hover color */
			$primaryButtonTextHoverColor = $this->getStoreConfig('themesettings/main/primary_button_text_hover_color', $storeId);
			if($primaryButtonTextHoverColor!=''){
				$html .= 'button.primary:hover{color:'.$primaryButtonTextHoverColor.'}';
			}
			
			/* Background color */
			$primaryButtonBackgroundColor = $this->getStoreConfig('themesettings/main/primary_button_background_color', $storeId);
			if($primaryButtonBackgroundColor!=''){
				$html .= 'button.primary{background-color:'.$primaryButtonBackgroundColor.'}';
			}
			/* Background hover color */
			$primaryButtonBackgroundHoverColor = $this->getStoreConfig('themesettings/main/primary_button_background_hover_color', $storeId);
			if($primaryButtonBackgroundHoverColor!=''){
				$html .= 'button.primary:hover{background-color:'.$primaryButtonBackgroundHoverColor.'}';
			}
			
			/* Border color */
			$primaryButtonBorderColor = $this->getStoreConfig('themesettings/main/primary_button_border_color', $storeId);
			if($primaryButtonBorderColor!=''){
				$html .= 'button.primary{color:'.$primaryButtonBorderColor.'}';
			}
			/* Border hover color */
			$primaryButtonBorderHoverColor = $this->getStoreConfig('themesettings/main/primary_button_border_hover_color', $storeId);
			if($primaryButtonBorderHoverColor!=''){
				$html .= 'button.primary:hover{color:'.$primaryButtonBorderHoverColor.'}';
			}
		}
		
		/* Secondary Button */
		if($this->getStoreConfig('themesettings/main/custom_secondary_button', $storeId)){
			/* Text color */
			$secondaryButtonTextColor = $this->getStoreConfig('themesettings/main/secondary_button_text_color', $storeId);
			if($secondaryButtonTextColor!=''){
				$html .= 'button.secondary{color:'.$secondaryButtonTextColor.'}';
			}
			/* Text hover color */
			$secondaryButtonTextHoverColor = $this->getStoreConfig('themesettings/main/secondary_button_text_hover_color', $storeId);
			if($secondaryButtonTextHoverColor!=''){
				$html .= 'button.secondary:hover{color:'.$secondaryButtonTextHoverColor.'}';
			}
			
			/* Background color */
			$secondaryButtonBackgroundColor = $this->getStoreConfig('themesettings/main/secondary_button_background_color', $storeId);
			if($secondaryButtonBackgroundColor!=''){
				$html .= 'button.secondary{background-color:'.$secondaryButtonBackgroundColor.'}';
			}
			/* Background hover color */
			$secondaryButtonBackgroundHoverColor = $this->getStoreConfig('themesettings/main/secondary_button_background_hover_color', $storeId);
			if($secondaryButtonBackgroundHoverColor!=''){
				$html .= 'button.secondary:hover{background-color:'.$secondaryButtonBackgroundHoverColor.'}';
			}
			
			/* Border color */
			$secondaryButtonBorderColor = $this->getStoreConfig('themesettings/main/secondary_button_border_color', $storeId);
			if($secondaryButtonBorderColor!=''){
				$html .= 'button.secondary{color:'.$secondaryButtonBorderColor.'}';
			}
			/* Border hover color */
			$secondaryButtonBorderHoverColor = $this->getStoreConfig('themesettings/main/secondary_button_border_hover_color', $storeId);
			if($secondaryButtonBorderHoverColor!=''){
				$html .= 'button.secondary:hover{color:'.$secondaryButtonBorderHoverColor.'}';
			}
		}
		
		/* Footer */
		$html .= 'footer.page-footer{';
		/* Footer Background */
		if($this->getStoreConfig('themesettings/footer/custom_footer_background', $storeId)){
			$backgroundColor = $this->getStoreConfig('themesettings/footer/background_color', $storeId);
			$backgroundImage = $this->getStoreConfig('themesettings/footer/background_image', $storeId);
			
			if($backgroundColor!=''){
				$html .= 'background-color:'.$backgroundColor.';';
			}
			
			if($backgroundImage!=''){
				$backgroundImageUrl = $this->getMediaUrl('mgs/background/'.$backgroundImage);

				$html .= 'background-image:url('.$backgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/footer/background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/footer/background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/footer/background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/footer/background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
		}
		/* Footer Border */
		if($this->getStoreConfig('themesettings/footer/custom_footer_border', $storeId)){
			$borderTopSize = $this->getStoreConfig('themesettings/footer/border_top_size', $storeId);
			$borderTopColor = $this->getStoreConfig('themesettings/footer/border_top_color', $storeId);
			if($borderTopSize !='' && $borderTopColor!=''){
				$html .= 'border-top:'.$borderTopSize.'px solid '.$borderTopColor.';';
			}
			
			$borderBottomSize = $this->getStoreConfig('themesettings/footer/border_bottom_size', $storeId);
			$borderBottomColor = $this->getStoreConfig('themesettings/footer/border_bottom_color', $storeId);
			if($borderBottomSize !='' && $borderBottomColor!=''){
				$html .= 'border-bottom:'.$borderBottomSize.'px solid '.$borderBottomColor.';';
			}
		}
		
		$html .= '}';
		
		
		/* Top Footer */
		if($this->getStoreConfig('themesettings/footer/custom_footer_top', $storeId)){
			$html .= 'footer.page-footer .top-footer{';
			
			/* Background */
			$topFooterBackgroundColor = $this->getStoreConfig('themesettings/footer/footer_top_background_color', $storeId);
			$topFooterBackgroundImage = $this->getStoreConfig('themesettings/footer/footer_top_background_image', $storeId);
			if($topFooterBackgroundColor!=''){
				$html .= 'background-color:'.$topFooterBackgroundColor.';';
			}
			if($topFooterBackgroundImage!=''){
				$topFooterBackgroundImageUrl = $this->getMediaUrl('mgs/background/'.$topFooterBackgroundImage);

				$html .= 'background-image:url('.$topFooterBackgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/footer/footer_top_background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/footer/footer_top_background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/footer/footer_top_background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/footer/footer_top_background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
			
			/* Text color*/
			$topFooterTextColor = $this->getStoreConfig('themesettings/footer/footer_top_text_color', $storeId);
			if($topFooterTextColor!=''){
				$html .= 'color:'.$topFooterTextColor.';';
			}
			$html .= '}';
			
			/* Link color */
			$topFooterLinkColor = $this->getStoreConfig('themesettings/footer/footer_top_link_color', $storeId);
			$topFooterLinkHoverColor = $this->getStoreConfig('themesettings/footer/footer_top_link_hover_color', $storeId);
			if($topFooterLinkColor!=''){
				$html .= 'footer.page-footer .top-footer a{color:'.$topFooterLinkColor.';}';
			}
			if($topFooterLinkHoverColor!=''){
				$html .= 'footer.page-footer .top-footer a:hover{color:'.$topFooterLinkHoverColor.';}';
			}
			
			/* Icon color */
			$topFooterIconColor = $this->getStoreConfig('themesettings/footer/footer_top_icon_color', $storeId);
			if($topFooterIconColor!=''){
				$html .= 'footer.page-footer .top-footer .theme-footer-icon{color:'.$topFooterIconColor.';}';
			}
			
			/* Heading color */
			$topFooterHeadingColor = $this->getStoreConfig('themesettings/footer/footer_top_heading_color', $storeId);
			if($topFooterHeadingColor!=''){
				$html .= 'footer.page-footer .top-footer h2,footer.page-footer .top-footer h3,footer.page-footer .top-footer h4,footer.page-footer .top-footer h5,footer.page-footer .top-footer h6{color:'.$topFooterHeadingColor.';}';
			}
		}
		
		/* Middle Footer */
		if($this->getStoreConfig('themesettings/footer/custom_footer_middle', $storeId)){
			$html .= 'footer.page-footer .middle-footer{';
			
			/* Background */
			$middleFooterBackgroundColor = $this->getStoreConfig('themesettings/footer/footer_middle_background_color', $storeId);
			$middleFooterBackgroundImage = $this->getStoreConfig('themesettings/footer/footer_middle_background_image', $storeId);
			if($middleFooterBackgroundColor!=''){
				$html .= 'background-color:'.$middleFooterBackgroundColor.';';
			}
			if($middleFooterBackgroundImage!=''){
				$middleFooterBackgroundImageUrl = $this->getMediaUrl('mgs/background/'.$middleFooterBackgroundImage);

				$html .= 'background-image:url('.$middleFooterBackgroundImageUrl.');';

				if($this->getStoreConfig('themesettings/footer/footer_middle_background_cover', $storeId)){
					$html.= 'background-size:cover;';
				}else{
					$backgroundRepeat = $this->getStoreConfig('themesettings/footer/footer_middle_background_repeat', $storeId);
					$html.= 'background-repeat:'.$backgroundRepeat.';';
				}
				$backgroundPositionX = $this->getStoreConfig('themesettings/footer/footer_middle_background_position_x', $storeId);
				$backgroundPositionY = $this->getStoreConfig('themesettings/footer/footer_middle_background_position_y', $storeId);
				$html.= 'background-position:'.$backgroundPositionX.' '.$backgroundPositionY.';';
			}
			
			/* Text color*/
			$middleFooterTextColor = $this->getStoreConfig('themesettings/footer/footer_middle_text_color', $storeId);
			if($middleFooterTextColor!=''){
				$html .= 'color:'.$middleFooterTextColor.';';
			}
			$html .= '}';
			
			/* Link color */
			$middleFooterLinkColor = $this->getStoreConfig('themesettings/footer/footer_middle_link_color', $storeId);
			$middleFooterLinkHoverColor = $this->getStoreConfig('themesettings/footer/footer_middle_link_hover_color', $storeId);
			if($middleFooterLinkColor!=''){
				$html .= 'footer.page-footer .middle-footer a{color:'.$middleFooterLinkColor.';}';
			}
			if($middleFooterLinkHoverColor!=''){
				$html .= 'footer.page-footer .middle-footer a:hover{color:'.$middleFooterLinkHoverColor.';}';
			}
			
			/* Icon color */
			$middleFooterIconColor = $this->getStoreConfig('themesettings/footer/footer_middle_icon_color', $storeId);
			if($middleFooterIconColor!=''){
				$html .= 'footer.page-footer .middle-footer .theme-footer-icon{color:'.$middleFooterIconColor.';}';
			}
			
			/* Heading color */
			$middleFooterHeadingColor = $this->getStoreConfig('themesettings/footer/footer_middle_heading_color', $storeId);
			if($middleFooterHeadingColor!=''){
				$html .= 'footer.page-footer .middle-footer h2,footer.page-footer .middle-footer h3,footer.page-footer .middle-footer h4,footer.page-footer .middle-footer h5,footer.page-footer .middle-footer h6{color:'.$middleFooterHeadingColor.';}';
			}
		}
		
		/* Bottom Footer */
		if($this->getStoreConfig('themesettings/footer/custom_footer_bottom', $storeId)){
			$html .= 'footer.page-footer .bottom-footer{';
			
			/* Background */
			$bottomFooterBackgroundColor = $this->getStoreConfig('themesettings/footer/footer_bottom_background_color', $storeId);

			if($bottomFooterBackgroundColor!=''){
				$html .= 'background-color:'.$bottomFooterBackgroundColor.';';
			}
			
			/* Text color*/
			$bottomFooterTextColor = $this->getStoreConfig('themesettings/footer/footer_bottom_text_color', $storeId);
			if($bottomFooterTextColor!=''){
				$html .= 'color:'.$bottomFooterTextColor.';';
			}
			$html .= '}';
			
			/* Link color */
			$bottomFooterLinkColor = $this->getStoreConfig('themesettings/footer/footer_bottom_link_color', $storeId);
			$bottomFooterLinkHoverColor = $this->getStoreConfig('themesettings/footer/footer_bottom_link_hover_color', $storeId);
			if($bottomFooterLinkColor!=''){
				$html .= 'footer.page-footer .bottom-footer a{color:'.$bottomFooterLinkColor.';}';
			}
			if($bottomFooterLinkHoverColor!=''){
				$html .= 'footer.page-footer .bottom-footer a:hover{color:'.$bottomFooterLinkHoverColor.';}';
			}
			
			/* Icon color */
			$bottomFooterIconColor = $this->getStoreConfig('themesettings/footer/footer_bottom_icon_color', $storeId);
			if($bottomFooterIconColor!=''){
				$html .= 'footer.page-footer .bottom-footer .theme-footer-icon{color:'.$bottomFooterIconColor.';}';
			}
		}
		return $html;
	}
	
	public function getStyleInline($storeId){
		$html = '';
		$singleNavType = $this->getStoreConfig('fbuilder/single_slider/navigation', $storeId);
		$singleNavWidth = $this->getStoreConfig('fbuilder/single_slider/nav_width', $storeId);
		$singleNavHeight = $this->getStoreConfig('fbuilder/single_slider/nav_height', $storeId);
		$singleNavFontsize = $this->getStoreConfig('fbuilder/single_slider/nav_font_size', $storeId);
		$singleNavBorderRadius = $this->getStoreConfig('fbuilder/single_slider/border_radius', $storeId);
		
		$singleNavColor = $this->getStoreConfig('fbuilder/single_slider/color', $storeId);
		$singleNavBorder = $this->getStoreConfig('fbuilder/single_slider/border', $storeId);
		$singleNavBackground = $this->getStoreConfig('fbuilder/single_slider/background', $storeId);
		$singleNavBackgroundTransparent = $this->getStoreConfig('fbuilder/single_slider/background_transparent', $storeId);
		
		$singleNavHoverColor = $this->getStoreConfig('fbuilder/single_slider/hover_color', $storeId);
		$singleNavHoverBorder = $this->getStoreConfig('fbuilder/single_slider/hover_border', $storeId);
		$singleNavHoverBackground = $this->getStoreConfig('fbuilder/single_slider/hover_background', $storeId);
		$singleNavHoverBackgroundTransparent = $this->getStoreConfig('fbuilder/single_slider/hover_background_transparent', $storeId);
		
		$singleDotWidth = $this->getStoreConfig('fbuilder/single_slider/dot_width', $storeId);
		$singleDotHeight = $this->getStoreConfig('fbuilder/single_slider/dot_height', $storeId);
		$singleDotRadius = $this->getStoreConfig('fbuilder/single_slider/dot_radius', $storeId);
		$singleDotbackground = $this->getStoreConfig('fbuilder/single_slider/dot_background', $storeId);
		$singleDotActiveBackground = $this->getStoreConfig('fbuilder/single_slider/dot_active_background', $storeId);
		
		$lazyLoad = $this->getStoreConfig('themesettings/general/lazy_load', $storeId);
		$lazyLoadImg = $this->getStoreConfig('themesettings/general/lazy_img', $storeId);
		
		$html .= $this->getThemeSettingStyle($storeId);

		/* Single Slide */
		if($singleNavWidth!=''){
			$html .= '.mgs-carousel-single .owl-nav button img{width:'.$singleNavWidth.'px}';
			$html .= '.mgs-carousel-single .owl-nav button span{width:'.$singleNavWidth.'px}';
		}
		
		if($lazyLoad && $lazyLoadImg != ""){
			$html .= '.parent_lazy:not(.lazy_loaded), .intro-slider .page-header-sliders.loading:before { background-image: url("'. $this->getMediaUrl() . 'mgs/setting/default/' . $lazyLoadImg.'") !important; }';
		}
		
		if($singleNavHeight!=''){
			$html .= '.mgs-carousel-single .owl-nav button span, .mgs-carousel-single .owl-nav button span em{height:'.$singleNavHeight.'px; line-height:'.$singleNavHeight.'px}';
			$html .= '.mgs-carousel-single.nav-position-middle-outside .owl-nav button span, .mgs-carousel-single.nav-position-middle-inside .owl-nav button span{margin-top:-' . ($singleNavHeight/2) . 'px}';
		}
		if($singleNavType=='font'){
			if($singleNavFontsize!=''){
				$html .= '.mgs-carousel-single .owl-nav button span em{font-size:'.$singleNavFontsize.'px;}';
			}
			if($singleNavColor!=''){
				$html .= '.mgs-carousel-single .owl-nav button span em{color:'.$singleNavColor.'}';
			}
			if($singleNavBorder!=''){
				$html .= '.mgs-carousel-single .owl-nav button span{border:1px solid '.$singleNavBorder.'}';
			}
			if($singleNavBorderRadius!=''){
				$html .= '.mgs-carousel-single .owl-nav button span{border-radius:'.$singleNavBorderRadius.'px}';
			}
			if($singleNavBackground!=''){
				if($singleNavBackgroundTransparent>0 && $singleNavBackgroundTransparent<1){
					list($r, $g, $b) = sscanf($singleNavBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-single .owl-nav button span{background-color:rgba('.$r.', '.$g.', '.$b.', '.$singleNavBackgroundTransparent.')}';
				}else{
					$html .= '.mgs-carousel-single .owl-nav button span{background:'.$singleNavBackground.'}';
				}
			}
			
			if($singleNavHoverColor!=''){
				$html .= '.mgs-carousel-single .owl-nav button span:hover em{color:'.$singleNavHoverColor.'}';
			}
			if($singleNavHoverBorder!=''){
				$html .= '.mgs-carousel-single .owl-nav button span:hover{border:1px solid '.$singleNavHoverBorder.'}';
			}
			if($singleNavHoverBackground!=''){
				if($singleNavHoverBackgroundTransparent!=''){
					list($r, $g, $b) = sscanf($singleNavHoverBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-single .owl-nav button span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$singleNavHoverBackgroundTransparent.')}';
				}else{
					if($singleNavBackgroundTransparent>0 && $singleNavBackgroundTransparent<1){
						list($r, $g, $b) = sscanf($singleNavHoverBackground, "#%02x%02x%02x");
						$html .= '.mgs-carousel-single .owl-nav button span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$singleNavBackgroundTransparent.')}';
					}else{
						$html .= '.mgs-carousel-single .owl-nav button span:hover{background:'.$singleNavHoverBackground.'}';
					}
				}
			}
		}

		if($singleDotWidth!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{width:'.$singleDotWidth.'px}';
		}
		if($singleDotHeight!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{height:'.$singleDotHeight.'px}';
		}
		if($singleDotRadius!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{border-radius:'.$singleDotRadius.'px}';
		}
		if($singleDotbackground!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot span{background:'.$singleDotbackground.'}';
		}
		if($singleDotActiveBackground!=''){
			$html .= '.mgs-carousel-single .owl-dots .owl-dot.active span, .mgs-carousel-single .owl-dots .owl-dot span:hover{background:'.$singleDotActiveBackground.'}';
		}
		
		
		/* Multiple Slide */
		$multipleNavType = $this->getStoreConfig('fbuilder/multiple_slider/navigation', $storeId);
		$multipleNavWidth = $this->getStoreConfig('fbuilder/multiple_slider/nav_width', $storeId);
		$multipleNavHeight = $this->getStoreConfig('fbuilder/multiple_slider/nav_height', $storeId);
		$multipleNavFontsize = $this->getStoreConfig('fbuilder/multiple_slider/nav_font_size', $storeId);
		$multipleNavBorderRadius = $this->getStoreConfig('fbuilder/multiple_slider/border_radius', $storeId);
		
		$multipleNavColor = $this->getStoreConfig('fbuilder/multiple_slider/color', $storeId);
		$multipleNavBorder = $this->getStoreConfig('fbuilder/multiple_slider/border', $storeId);
		$multipleNavBackground = $this->getStoreConfig('fbuilder/multiple_slider/background', $storeId);
		$multipleNavBackgroundTransparent = $this->getStoreConfig('fbuilder/multiple_slider/background_transparent', $storeId);
		
		$multipleNavHoverColor = $this->getStoreConfig('fbuilder/multiple_slider/hover_color', $storeId);
		$multipleNavHoverBorder = $this->getStoreConfig('fbuilder/multiple_slider/hover_border', $storeId);
		$multipleNavHoverBackground = $this->getStoreConfig('fbuilder/multiple_slider/hover_background', $storeId);
		$multipleNavHoverBackgroundTransparent = $this->getStoreConfig('fbuilder/multiple_slider/hover_background_transparent', $storeId);
		
		$multipleDotWidth = $this->getStoreConfig('fbuilder/multiple_slider/dot_width', $storeId);
		$multipleDotHeight = $this->getStoreConfig('fbuilder/multiple_slider/dot_height', $storeId);
		$multipleDotRadius = $this->getStoreConfig('fbuilder/multiple_slider/dot_radius', $storeId);
		$multipleDotbackground = $this->getStoreConfig('fbuilder/multiple_slider/dot_background', $storeId);
		$multipleDotActiveBackground = $this->getStoreConfig('fbuilder/multiple_slider/dot_active_background', $storeId);

		/* Single Slide */
		if($multipleNavWidth!=''){
			$html .= '.mgs-carousel-multiple .owl-nav button img{width:'.$multipleNavWidth.'px}';
			$html .= '.mgs-carousel-multiple .owl-nav button span{width:'.$multipleNavWidth.'px}';
		}
		
		if($multipleNavHeight!=''){
			$html .= '.mgs-carousel-multiple .owl-nav button span, .mgs-carousel-multiple .owl-nav button span em{height:'.$multipleNavHeight.'px; line-height:'.$multipleNavHeight.'px}';
			$html .= '.mgs-carousel-multiple.nav-position-middle-outside .owl-nav button span, .mgs-carousel-multiple.nav-position-middle-inside .owl-nav button span{margin-top:-' . ($multipleNavHeight/2) . 'px}';
		}
		if($multipleNavType=='font'){
			if($multipleNavFontsize!=''){
				$html .= '.mgs-carousel-multiple .owl-nav button span em{font-size:'.$multipleNavFontsize.'px;}';
			}
			if($multipleNavColor!=''){
				$html .= '.mgs-carousel-multiple .owl-nav button span em{color:'.$multipleNavColor.'}';
			}
			if($multipleNavBorder!=''){
				$html .= '.mgs-carousel-multiple .owl-nav button span{border:1px solid '.$multipleNavBorder.'}';
			}
			if($multipleNavBorderRadius!=''){
				$html .= '.mgs-carousel-multiple .owl-nav button span{border-radius:'.$multipleNavBorderRadius.'px}';
			}
			if($multipleNavBackground!=''){
				if($multipleNavBackgroundTransparent>0 && $multipleNavBackgroundTransparent<1){
					list($r, $g, $b) = sscanf($multipleNavBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-multiple .owl-nav button span{background-color:rgba('.$r.', '.$g.', '.$b.', '.$multipleNavBackgroundTransparent.')}';
				}else{
					$html .= '.mgs-carousel-multiple .owl-nav button span{background:'.$multipleNavBackground.'}';
				}
			}
			
			if($multipleNavHoverColor!=''){
				$html .= '.mgs-carousel-multiple .owl-nav button span:hover em{color:'.$multipleNavHoverColor.'}';
			}
			if($multipleNavHoverBorder!=''){
				$html .= '.mgs-carousel-multiple .owl-nav button span:hover{border:1px solid '.$multipleNavHoverBorder.'}';
			}
			if($multipleNavHoverBackground!=''){
				if($multipleNavHoverBackgroundTransparent!=''){
					list($r, $g, $b) = sscanf($multipleNavHoverBackground, "#%02x%02x%02x");
					$html .= '.mgs-carousel-multiple .owl-nav button span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$multipleNavHoverBackgroundTransparent.')}';
				}else{
					if($multipleNavBackgroundTransparent>0 && $multipleNavBackgroundTransparent<1){
						list($r, $g, $b) = sscanf($multipleNavHoverBackground, "#%02x%02x%02x");
						$html .= '.mgs-carousel-multiple .owl-nav button span:hover{background-color:rgba('.$r.', '.$g.', '.$b.', '.$multipleNavBackgroundTransparent.')}';
					}else{
						$html .= '.mgs-carousel-multiple .owl-nav button span:hover{background:'.$multipleNavHoverBackground.'}';
					}
				}
			}
		}

		if($multipleDotWidth!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{width:'.$multipleDotWidth.'px}';
		}
		if($multipleDotHeight!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{height:'.$multipleDotHeight.'px}';
		}
		if($multipleDotRadius!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{border-radius:'.$multipleDotRadius.'px}';
		}
		if($multipleDotbackground!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot span{background:'.$multipleDotbackground.'}';
		}
		if($multipleDotActiveBackground!=''){
			$html .= '.mgs-carousel-multiple .owl-dots .owl-dot.active span, .mgs-carousel-multiple .owl-dots .owl-dot span:hover{background:'.$multipleDotActiveBackground.'}';
		}
		
		$sectionWidth = $this->getStoreConfig('fbuilder/general/container_width', $storeId);
		$columnPadding = $this->getStoreConfig('fbuilder/general/column_padding', $storeId);
		
		if(($sectionWidth!='') && ($sectionWidth>992)){
			if($sectionWidth!=1240){
				$html .= '.cms-index-index.active-builder #maincontent .section-builder .frame, .cms-page-view.active-builder #maincontent .section-builder .frame{max-width:'.$sectionWidth.'px; padding-left:'.$columnPadding.'px; padding-right:'.$columnPadding.'px}';
			}
		}else{
			$sectionWidth = 1240;
		}
		
		
		
		/* Theme Settings */
		$width = $this->getStoreConfig('themesettings/general/width', $storeId);
		if($width=='custom'){
			$customWidth = $this->getStoreConfig('themesettings/general/custom_width', $storeId);
			
			$html .= $this->generateCssCustomWidth($customWidth);
			
			if($this->getStoreConfig('themesettings/general/layout', $storeId) == "boxed"){
				$_maxWidth = $customWidth + 60;
				$html .= 'body.boxed:not(.fbuilder-create-element) > .page-wrapper { max-width: '. $customWidth .'px; } @media (max-width: '. $_maxWidth .'px){ body.boxed:not(.fbuilder-create-element) > .page-wrapper { max-width: calc(100% - 60px); } }';
			}
		}
		
		$html .= $this->getStoreConfig('fbuilder/font_style/custom_style', $storeId);
		return $html;
	}
	
	public function generateCssCustomWidth($customWidth){
		return '.custom .navigation, .custom .breadcrumbs, .custom .page-header .header.panel, .custom .header.content, .custom .footer.content, .custom .page-wrapper > .widget, .custom .page-wrapper > .page-bottom, .custom .block.category.event, .custom .top-container, .custom .page-main{max-width: '. $customWidth .'px;}';
	}

	public function getPathImageLazyLoad() {
		return "MGS_Fbuilder::images/blank.png";
	}
}