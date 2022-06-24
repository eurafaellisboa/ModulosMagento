<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MGS\MollaTheme\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Api\ProductRepositoryInterface;
/**
 * Contact base helper
 */
class Config extends \MGS\ThemeSettings\Helper\Config
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
	protected $_categoryCollectionFactory;
	protected $categoryRepository;  
	
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Url $url,
		ProductRepositoryInterface $productRepository,
		\Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
	) {
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
		$this->_categoryCollectionFactory = $categoryCollectionFactory;
		parent::__construct($context, $date, $request, $filesystem, $objectManager, $storeManager, $url);
		
	}
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
		
		/* Color Primary */
		$colorPrimary = $this->getStoreConfig('themesettings/general/color_primary', $storeId);
		if($colorPrimary!=''){
			
			$html .= '.price-box .price {color:'.$colorPrimary.'}';
			
			$html .= 'a:hover, a:focus, .text-primary, .text-primary-hover:hover, .header-area .horizontal-menu .mgs-megamenu--main ul li:hover > a, .header-area .horizontal-menu .mgs-megamenu--main ul li.active > a, .menu-banner:hover .title, .header-area .horizontal-menu .nav-main-menu .dropdown-mega-menu > .dropdown-submenu-ct > li > a , .btn.btn-default:not(:hover), .action.btn-default:not(:hover), .single-deal .product-item-info.template-2 .product-item-details .action.tocart , .page-header .header-area .customer-web-config .switcher .action:hover, .products-grid.effect4 .items .product-item .product-item-inner .product-item-actions .action:hover, .cart.table-wrapper td.subtotal, .cart.table-wrapper .edit-qty:hover, .cart.line .action:hover, .coupon .actions-toolbar .primary .apply:hover, .opc-wrapper .form-discount .actions-toolbar .primary .action:hover , .block.related .products-grid.effect4 .items .product-item .product-item-inner .product-item-actions .action.quickview, .custom.account.page-layout-2columns-left .page-main .sidebar .account-nav .content .items .item.current strong, .custom.account.page-layout-2columns-left .page-main .sidebar-additional .block .block-content #cart-sidebar-reorder .product-item .product-item-name a span:hover, .toolbar .pages .pages-items .item.current strong.page, .vertical-menu .category-menu .dropdown-menu .sub-menu .mega-menu-sub-title , .products-grid.effect6 .items .product-item .product-item-actions > .actions-primary .action.tocart:hover, .mobile-menu-wrapper .nav-main-menu li._show-child > a, .mobile-menu-wrapper .nav-main-menu li._show-child > .toggle-menu .icon-toggle:after, .products-grid.effect6 .items .product-item .product-item-actions > .actions-secondary .action:hover:before, .vertical-outsite-header .category-dropdown .vertical-menu > li:hover > a, .vertical-outsite-header .category-dropdown .vertical-menu > li:hover > .toggle-menu a , .products-grid.effect7 .items .product-item .action.tocart, .products-grid.effect7 .items .product-item .product-item-actions > .actions-secondary .action:before, .single-deal .product-item-info.template-3 .product-item-details .action.tocart, .single-deal .product-item-info.template-3 .actions-secondary .action, .authentication-wrapper .action-auth-toggle:not(:hover), .page-header .header-area .block-search .action.search:hover, .page-header .header3 .conts-top-header .top-menu .top-wishlist .action .counter.qty, .header-area.header6 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before , .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before{color:'.$colorPrimary.' !important}';
			
			$html .= '.btn.btn-primary, .action.primary , .page-header .header-area .minicart-wrapper .minicart-items .product-item > .product .product-item-details .details-qty .update-cart-item , .owl-carousel .owl-dots .owl-dot:hover span  , .products-grid.effect1 .product-item .product-item-actions > .actions-secondary .action span, .products-grid.effect3 .product-item .product-item-actions > .actions-secondary .action span, .single-deal .product-item-info.template-1 .product-item-details .deal-timer > div > span, .page-header .header-area .top-wishlist .action .counter.qty, .page-header .header-area .minicart-wrapper .action.showcart .counter.qty, .products-grid.effect1 .product-item .product-item-actions > .actions-secondary .action:hover, .products-grid.effect3 .product-item .product-item-actions > .actions-secondary .action:hover , .btn.btn-default:hover, .action.btn-default:hover, .btn.btn-default._hover, .action.btn-default._hover, .btn.btn-default:focus, .action.btn-default:focus , .header-area.header1 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before , .products-grid.effect4 .items .product-item .product-item-info .action-wishlist .action.towishlist, .authentication-wrapper .action-auth-toggle:hover, .authentication-wrapper .action-auth-toggle:focus, .block-authentication .actions-toolbar > .primary .action, .checkout-payment-method .payment-option-title .action-toggle:hover, .products-grid.effect4 .items .product-item .product-item-info .action-wishlist .action.towishlist span, .page-header .header8.header-area .horizontal-menu .mgs-megamenu--main > ul > li > a:before , .category-dropdown .title-vertical-menu:hover, .dark-primary , .category-dropdown:not(.category-market):hover .title-vertical-menu, .category-dropdown:not(.category-market):focus .title-vertical-menu, .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, header.page-header .search-mobile .block-search .action.search, .products-grid.effect6 .items .product-item .product-item-actions > .actions-secondary .action:not(:hover), .bg-primary, .products-grid.effect5 .items .product-item .product-item-actions > .actions-secondary .action, .products-grid.effect5 .items .product-item .product-item-actions > .actions-secondary .action:hover, .products-grid.effect5 .items .product-item .product-item-actions > .actions-secondary .action span, .single-deal .product-item-info.template-3 .actions-secondary .action:hover , .page-header .header6 .block-search .block-content .action.search , .page-header .header-area .compare-header .block-compare .block-title .counter.qty , .category-dropdown.category-market.active-cate .title-vertical-menu, .category-dropdown.category-market.active-cate-sticky .title-vertical-menu , .header-area.header1 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before {background-color: '.$colorPrimary.' !important}';
			
			$html .= '.btn.btn-primary, .action.primary , .page-header .header-area .minicart-wrapper .minicart-items .product-item > .product .product-item-details .details-qty .update-cart-item , .btn.btn-default, .action.btn-default , .owl-carousel .owl-dots .owl-dot:hover span , textarea:focus, select:focus, input[type="text"]:focus, input[type="password"]:focus, input[type="url"]:focus, input[type="tel"]:focus, input[type="search"]:focus, input[type="number"]:focus, input[type="datetime"]:focus, input[type="email"]:focus, .authentication-wrapper .action-auth-toggle, .authentication-wrapper .action-auth-toggle:hover, .authentication-wrapper .action-auth-toggle:focus, .block-authentication .actions-toolbar > .primary .action, .checkout-payment-method .payment-option-title .action-toggle:hover, .products-grid.effect5 .items .product-item .product-item-actions > .actions-secondary .action:hover , .products-grid.effect7 .items .product-item .action.tocart, .single-deal .product-item-info.template-3 .product-item-details .action.tocart , .page-header .header6 .block-search .block-content input {border-color: '.$colorPrimary.' !important}';
			
			$html .= '.bg-primary-hover:hover, .btn-outline-gray:hover, .btn-default-white:hover, .btn-default-dark:hover, .btn-default:hover , .products-grid.effect7 .items .product-item .product-item-actions > .actions-secondary .action:hover , .products-grid.effect7 .items .product-item .product-item-actions > .actions-secondary .action:hover:before, .action.subscribe.primary.btn.btn-default {color: #fff !important; border-color: '.$colorPrimary.' !important; background-color: '.$colorPrimary.' !important}';
			
			$html .= '.products-grid.effect7 .items .product-item .action.tocart:hover, .single-deal .product-item-info.template-3 .product-item-details .action.tocart:hover  {color: #333 !important; border-color: '.$colorPrimary.' !important; background-color: '.$colorPrimary.' !important}';
			
			$html .= '.customer-account-login .page-main .columns .form-box .form-tab .nav.nav-pills .nav-item .nav-link.active {border-bottom-color: '. $colorPrimary .' !important;}';
		}
		
		
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

		$html .= '.expert-training{';

		if($this->getStoreConfig('themesettings/general/custom_background', $storeId)) {
			$backgroundColor = $this->getStoreConfig('themesettings/general/background_color', $storeId);
			if($backgroundColor!=''){
				$html .= 'background-color:'.$backgroundColor.'!important;';
			}
		}
		$html .= '}';

		$html .= '.about-us-list-brand{';

		if($this->getStoreConfig('themesettings/general/custom_background', $storeId)) {
			$backgroundColor = $this->getStoreConfig('themesettings/general/background_color', $storeId);
			if($backgroundColor!=''){
				$html .= 'background-color:'.$backgroundColor.'!important;';
			}
		}
		$html .= '}';
		
		/* Header */
		$html .= 'header.page-header .header-area , .active-sticky.start-stk.header4 .elements-sticky, .active-sticky.start-stk.header7 .elements-sticky, .active-sticky.start-stk.header6 .elements-sticky {';
		if($this->getStoreConfig('themesettings/header/custom_header_background', $storeId)){
			$backgroundColor = $this->getStoreConfig('themesettings/header/background_color', $storeId);
			$backgroundImage = $this->getStoreConfig('themesettings/header/background_image', $storeId);
			
			if($backgroundColor!=''){
				$html .= 'background-color:'.$backgroundColor.' !important;';
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
			$html .= 'header .header-area .top-header{';
			
			/* Top Header: Background */
			$topHeaderBackgroundColor = $this->getStoreConfig('themesettings/header/top_header_background_color', $storeId);
			$topHeaderBackgroundImage = $this->getStoreConfig('themesettings/header/top_header_background_image', $storeId);
			if($topHeaderBackgroundColor!=''){
				$html .= 'background-color:'.$topHeaderBackgroundColor.'  !important;';
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
				$html .= 'color:'.$topHeaderTextColor.'  !important;';
			}
			$html .= '}';
			
			/* Top Header: Link */
			$topHeaderLinkColor = $this->getStoreConfig('themesettings/header/top_header_link_color', $storeId);
			$topHeaderLinkHoverColor = $this->getStoreConfig('themesettings/header/top_header_link_hover_color', $storeId);
			if($topHeaderLinkColor!=''){
				$html .= 'header .top-header a{color:'.$topHeaderLinkColor.' !important;}';
			}
			if($topHeaderLinkHoverColor!=''){
				$html .= 'header .top-header a:hover{color:'.$topHeaderLinkHoverColor.'!important;;}';
			}
		}
		
		/* Middle Header */
		if($this->getStoreConfig('themesettings/header/custom_middle_header', $storeId)){
			$html .= 'header .header-area .middle-header{';
			
			/* Middle Header: Background */
			$middleHeaderBackgroundColor = $this->getStoreConfig('themesettings/header/middle_header_background_color', $storeId);
			$middleHeaderBackgroundImage = $this->getStoreConfig('themesettings/header/middle_header_background_image', $storeId);
			if($middleHeaderBackgroundColor!=''){
				$html .= 'background-color:'.$middleHeaderBackgroundColor.'!important;';
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
				$html .= 'color:'.$middleHeaderTextColor.'!important;';
			}
			$html .= '}';
			
			/* Middle Header: Link */
			$middleHeaderLinkColor = $this->getStoreConfig('themesettings/header/middle_header_link_color', $storeId);
			$middleHeaderLinkHoverColor = $this->getStoreConfig('themesettings/header/middle_header_link_hover_color', $storeId);
			if($middleHeaderLinkColor!=''){
				$html .= 'header .header-area .middle-header a{color:'.$middleHeaderLinkColor.' !important;}';
			}
			if($middleHeaderLinkHoverColor!=''){
				$html .= 'header .header-area .middle-header a:hover{color:'.$middleHeaderLinkHoverColor.' !important;}';
			}
			
			/* Middle Header: Icon */
			$middleHeaderIconColor = $this->getStoreConfig('themesettings/header/middle_header_icon_color', $storeId);
			$middleHeaderIconHoverColor = $this->getStoreConfig('themesettings/header/middle_header_icon_hover_color', $storeId);
			if($middleHeaderIconColor!=''){
				$html .= 'header .header-area .middle-header .theme-header-icon{color:'.$middleHeaderIconColor.' !important;}';
			}
			if($middleHeaderIconHoverColor!=''){
				$html .= 'header .header-area .middle-header .theme-header-icon:hover,header .header-area .middle-header .block-search.active .theme-header-icon, header .header-area .middle-header .setting-site.active .theme-header-icon,header .header-area .middle-header .minicart-wrapper.active .theme-header-icon,header .header-area .middle-header .header-top-links.active .theme-header-icon{color:'.$middleHeaderIconHoverColor.' !important;}';
			}
		}
		
		/* Bottom Header */
		if($this->getStoreConfig('themesettings/header/bottom_header_custom', $storeId)){
			$html .= 'header .bottom-header{';
			
			/* Bottom Header: Background */
			$bottomHeaderBackgroundColor = $this->getStoreConfig('themesettings/header/bottom_header_background_color', $storeId);
			$bottomHeaderBackgroundImage = $this->getStoreConfig('themesettings/header/bottom_header_background_image', $storeId);
			if($bottomHeaderBackgroundColor!=''){
				$html .= 'background-color:'.$bottomHeaderBackgroundColor.' !important;';
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
				$html .= 'color:'.$bottomHeaderTextColor.' !important;';
			}
			$html .= '}';
			
			/* Bottom Header: Link */
			$bottomHeaderLinkColor = $this->getStoreConfig('themesettings/header/bottom_header_link_color', $storeId);
			$bottomHeaderLinkHoverColor = $this->getStoreConfig('themesettings/header/bottom_header_link_hover_color', $storeId);
			if($bottomHeaderLinkColor!=''){
				$html .= 'header .bottom-header a{color:'.$bottomHeaderLinkColor.' !important;}';
			}
			if($bottomHeaderLinkHoverColor!=''){
				$html .= 'header .bottom-header a:hover{color:'.$bottomHeaderLinkHoverColor.' !important;}';
			}
			
			/* Bottom Header: Icon */
			$bottomHeaderIconColor = $this->getStoreConfig('themesettings/header/bottom_header_icon_color', $storeId);
			$bottomHeaderIconHoverColor = $this->getStoreConfig('themesettings/header/bottom_header_icon_hover_color', $storeId);
			if($bottomHeaderIconColor!=''){
				$html .= 'header .bottom-header .theme-header-icon{color:'.$bottomHeaderIconColor.';}';
			}
			if($bottomHeaderIconHoverColor!=''){
				$html .= 'header .bottom-header .theme-header-icon:hover,header .bottom-header .block-search.active .theme-header-icon, header .bottom-header .setting-site.active .theme-header-icon,header .bottom-header .minicart-wrapper.active .theme-header-icon,header .bottom-header .header-top-links.active .theme-header-icon{color:'.$bottomHeaderIconHoverColor.' !important;}';
			}
		}
		
		/*Mini Cart*/
		if($this->getStoreConfig('themesettings/header/mini_cart_custom', $storeId)){
			/*Mini Cart Icon*/
			$miniCartIconColor = $this->getStoreConfig('themesettings/header/cart_icon_color', $storeId);
			$miniCartIconHoverColor = $this->getStoreConfig('themesettings/header/cart_icon_hover_color', $storeId);
			
			if($miniCartIconColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper a.action.showcart::before{color:'.$miniCartIconColor.' !important;}';
			}
			if($miniCartIconHoverColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper a.action.showcart:hover::before{color:'.$miniCartIconHoverColor.' !important;}';
			}
			
			/*Mini Cart Number Text*/
			$miniCartNumberColor = $this->getStoreConfig('themesettings/header/cart_number_color', $storeId);
			if($miniCartNumberColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper a.action.showcart .counter.qty{color:'.$miniCartNumberColor.' !important;}';
			}
			
			/*Mini Cart Number Background*/
			$miniCartNumberBackground = $this->getStoreConfig('themesettings/header/cart_number_background_color', $storeId);
			if($miniCartNumberBackground!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper a.action.showcart .counter.qty{background:'.$miniCartNumberBackground.' !important;}';
			}
			
			/*Mini Cart Text Color*/
			$miniCartTextColor = $this->getStoreConfig('themesettings/header/cart_text_color', $storeId);
			if($miniCartTextColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper .block-minicart .subtitle.empty span, header.page-header .header-area .minicart-wrapper .block-content > .actions > .subtotal > span.label, header.page-header .header-area .minicart-wrapper .minicart-items .product-item-details .product-item-name a, header.page-header .header-area .minicart-wrapper .minicart-items .product .actions a:before, header.page-header .header-area .minicart-wrapper .minicart-items .product-item-pricing .details-qty .label{color:'.$miniCartTextColor.' !important;}';
			}
			
			/*Mini Cart Heading Color*/
			$miniCartHeadingColor = $this->getStoreConfig('themesettings/header/cart_heading_color', $storeId);
			if($miniCartHeadingColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper .top-cart-title, .page-header .header-area .minicart-wrapper .block-minicart .subtotal .label{color:'.$miniCartHeadingColor.' !important;}';
			}
			
			/*Mini Cart Background Color*/
			$miniCartBackgroundColor = $this->getStoreConfig('themesettings/header/cart_background_color', $storeId);
			if($miniCartBackgroundColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper.active .block-minicart{background:'.$miniCartBackgroundColor.' !important;}';
			}
			
			/*Mini Cart Close Icon Color*/
			$miniCartCloseColor = $this->getStoreConfig('themesettings/header/cart_close_icon_color', $storeId);
			if($miniCartCloseColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper .block-content .action.close, header.page-header .minicart-wrapper .block-content .action.close:before{color:'.$miniCartCloseColor.' !important;}';
			}
			
			/*Mini Cart Divide Border Color*/
			$miniCartDivideBorderColor = $this->getStoreConfig('themesettings/header/cart_divide_border_color', $storeId);
			if($miniCartDivideBorderColor!=''){
				$html .= 'header.page-header .header-area .minicart-wrapper .top-cart-title, header.page-header .minicart-wrapper .minicart-items-wrapper .product-item+.product-item{border-color:'.$miniCartDivideBorderColor.' !important;}';
			}
		}
		
		/*Search*/
		if($this->getStoreConfig('themesettings/header/search_custom', $storeId)){
			/*Text Color*/
			$html .= 'header.page-header .block-search .block-content input{';
			$searchTextColor = $this->getStoreConfig('themesettings/header/search_text_color', $storeId);
			if($searchTextColor!=''){
				$html .= 'color:'.$searchTextColor.' !important;';
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
				$html .= '.page-header .header-area .header-top-links .login-form{background-color:'.$myAccountBackgroundColor.'}';
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
				$html .= '.page-header .switcher .options ul.dropdown, header.page-header .setting-site.active .setting-site-content, header.page-header .setting-site .setting-site-content .actions-close{background-color: rgba('.$r.','.$g.','.$b.','.$dropdownOpacity.') !important; border:none}';
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
				$html .= '.contact-index-index h2 {color:' . $mainTextColor . '}';
				$html .= '.contact-index-index span {color:' . $mainTextColor . ' !important}';
				$html .= '.contact-index-index p {color:' . $mainTextColor . ' !important}';
				$html .= '.about-us .about-welcome h4 {color:' . $mainTextColor . '!important}';
				$html .= '.about-us .about-welcome h2 {color:' . $mainTextColor . '!important}';
				$html .= '.why-choose-us .choose-us-content h2 {color:' . $mainTextColor . '!important}';
				$html .= '.why-choose-us .choose-us-content p {color:' . $mainTextColor . '!important}';
				$html .= '.mgs-heading h2 span {color:' . $mainTextColor . '!important}';
				$html .= '.client-say .testimonial-item .content blockquote {color:' . $mainTextColor . '!important}';
				$html .= '.client-say .testimonial-item .content .author-info .name {color:' . $mainTextColor . '!important}';
				$html .= '.client-say .testimonial-item .content .author-info .infomation {color:' . $mainTextColor . '!important}';
				$html .= '.mgs-profile .profile-info h4 {color:' . $mainTextColor . '!important}';
				$html .= '.mgs-profile .profile-info p {color:' . $mainTextColor . '!important}';
				$html .= '.progress .progress-value {color:' . $mainTextColor . '!important}';
				$html .= '.progress-info .progress-title, .portfolio-view .title, .portfolio-details .description .text-bold, p, .portfolio-details .portfolio-info .sharethis .text-color, .portfolio-details .portfolio-info .portfolio-datetime .date span, .portfolio-details .description .view-title .btn.btn-default, .locator-index-view .storelocator-details .store-opening-hours .title, .locator-index-view .storelocator-details .store-opening-hours .content table > tbody > tr td, .locator-index-view .storelocator-details .store-description table > tbody > tr > td, .locator-index-index .store-list-container .store-list .store-list-items .item-store-locator .store-infor address, .locator-index-index .store-list-container .store-list .store-list-items .title, .pages strong.page span, .contact-box h3, .contact-box address, .title, .lead.text-primary, .contact-info h3, .contact-list li, .store-list-container.store-list-contact .stores .item-store-locator .store-infor address, .store-list-container.store-list-contact .stores .item-store-locator .store-infor .store-subtitle, .store-list-container.store-list-contact .stores address, .store-list-container.store-list-contact .stores div, .about_testimonial .mgs-testimonial .testimonial-content .content blockquote,.about_testimonial .mgs-testimonial .testimonial-content .content .author-info .name.about_testimonial .mgs-testimonial .testimonial-content .content .author-info .name, .about_testimonial .mgs-testimonial .testimonial-content .content .author-info .infomation, .member .member-media .member-overlay .member-overlay-content .member-title, .member .member-media .member-overlay .member-overlay-content .member-title span, .member .member-content .member-title, .member .member-content .member-title span, .about_testimonial .mgs-testimonial .testimonial-content .content .author-info .name, .icon-box .icon-box-content .icon-box-title, .mgs-counter-box.box-horizontal .counter-box, .about_coutdown .counter-block .mgs-counter-box.box-horizontal .content, .about_coutdown .counter-block .mgs-counter-box.box-horizontal .counter-box .number, .about_coutdown .counter-block .mgs-counter-box.box-horizontal .counter-box .subtitle , .brand-index-index .shop-by-brand .count, .product.description ul {color:' . $mainTextColor . '!important}';
				$html .= '.customer-account-login .page-main .columns .form-box .form-tab .tab-content .form-group label, .customer-account-create .page-main .columns .form-box .form-tab .tab-content .form-group label, .customer-account-forgotpassword .page-main .columns .form-box .form-tab .tab-content .form-group label, .fieldset .field > .label > span, .field .control .input-text, .customer-account-login .page-main .columns .form-box .form-tab .tab-content .form-create-account fieldset .legend span, .customer-account-create .page-main .columns .form-box .form-tab .tab-content .form-create-account fieldset .legend span, .customer-account-forgotpassword .page-main .columns .form-box .form-tab .tab-content .form-create-account fieldset .legend span, .customer-account-login .page-main .columns .form-box .form-tab .tab-content .form-create-account label, .customer-account-create .page-main .columns .form-box .form-tab .tab-content .form-create-account label, .customer-account-forgotpassword .page-main .columns .form-box .form-tab .tab-content .form-create-account label, .custom.account.page-layout-2columns-left .page-main .main .block .block-title strong, .table:not(.cart):not(.totals) > thead > tr > th, .custom.account.page-layout-2columns-left .page-main .main .block .block-content .box-title span, address, .custom.account.page-layout-2columns-left .page-main .sidebar-additional .block .block-title strong, .custom.account.page-layout-2columns-left .page-main .main form .legend span {color: '.$mainTextColor.' !important}';
			}
			
			/* Link color */
			$mainLinkColor = $this->getStoreConfig('themesettings/main/link_color', $storeId);
			if($mainLinkColor!=''){
				$html .= 'a:visited, a, .blog-list a:visited, .blog-list a, .block-blog-categories a, .block-blog-posts a, .sidebar-additional .block-blog-posts li:before, .sidebar-additional .block-blog-categories li:before, .blog-view .post-actions, .product-info-main .product-addto-links a.btn-product, .product-options-bottom .product-addto-links a.btn-product, .product-sub-infomation .product-cat a, .product-reviews-summary .reviews-actions a.action.view, .product.info.detailed .product.data.items .item.title > a.switch {color:'.$mainLinkColor.'}';
				$html .= '.blog-post-item .post-aux a, .sidebar-additional .block-blog-tags .block-content .tag-cloud li {border-color:'.$mainLinkColor.'}';
				$html .= '.portfolio-category-view a, a > span,  .locator-index-index .store-list-container .store-list .store-list-items .action-toolbar .pager .pages-items .item.pages-item-next .action, .contact-box a:not(.social-icon), .shop-by-brand .characters-filter li > a {color: ' . $mainLinkColor .' !important;}';
				$html .= '.locator-index-index .store-list-container .store-list .store-list-items .action-toolbar .pager .pages-items .item.pages-item-next .action  {background-color: '. $mainLinkColor .' !important}';
			}
			
			/* Link hover color */
			$mainLinkHoverColor = $this->getStoreConfig('themesettings/main/link_hover_color', $storeId);
			if($mainLinkHoverColor!=''){

				$html .= 'a:hover, a:focus, .footer a:hover, .footer a:focus, .footer1 .widget-about-info a, .footer2 .widget-about-info a ,.footer3 .widget-call i, .blog-list a:hover, .blog-list a:focus, .sidebar-additional block-blog-tags .block-content .tag-cloud li:hover a, .sidebar-additional .block-blog-categories li:hover a, .sidebar-additional .block-blog-posts li:hover a, .sidebar-additional .block-blog-posts li:hover:before, .sidebar-additional .block-blog-categories li:hover:before, .blog-view .post-actions .prev-action:hover a, .blog-view .post-actions .next-action:hover a, .blog-view .post-actions .prev-action:hover i, .blog-view .post-actions .next-action:hover i, .header-area .horizontal-menu .mgs-megamenu--main ul li:hover > a, .header-area .horizontal-menu .nav-main-menu .dropdown-mega-menu .mega-menu-content .menu-col li:hover > a, .header-area .horizontal-menu .nav-main-menu .dropdown-mega-menu .dropdown-submenu-ct li:hover > a, .header-area .vertical-menu > li:hover > a, .header-area .vertical-menu > li.item-lead:hover > a, .header-area .horizontal-menu .mgs-megamenu--main .mega-menu-content .dropdown-menu-ct li:hover > a, .page-header .header-area .customer-web-config .switcher .action:hover,  .header-area .horizontal-menu .mgs-megamenu--main .dropdown-mega-menu .mega-menu-content .dropdown-menu-ct li:hover > a, .header-area .horizontal-menu .mgs-megamenu--main ul li:hover > a, .header-area .horizontal-menu .mgs-megamenu--main ul li.active > a, .menu-banner:hover .title, .header-area.header2 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, .vertical-menu .category-menu .dropdown-menu .sub-menu .mega-menu-sub-title, .page-header .header-area:not(.header6) .block-search .action.search:hover, .btn.btn-link , .btn.btn-default, .action.btn-default, .page-header .header2 .top-header .conts-top-header .customer-web-config .switcher .action:hover, .page-header .header2 .top-header .conts-top-header a:hover , .contact-box a:not(.social-icon):hover, .shop-by-brand .characters-filter li > a:hover, .product.info.detailed .product.data.items .item.title.active > a.switch, .review-add h4 , .page-header .header9 .top-header .conts-top-header .customer-web-config .switcher .action:hover, .page-header .header9 .top-header .conts-top-header a:hover{color:'.$mainLinkHoverColor.' }';
				
				$html .= 'a:hover, a:focus, .footer a:hover, .footer a:focus, .footer1 .widget-about-info a, .footer2 .widget-about-info a ,.footer3 .widget-call i, .blog-list a:hover, .blog-list a:focus, .sidebar-additional block-blog-tags .block-content .tag-cloud li:hover a, .sidebar-additional .block-blog-categories li:hover a, .sidebar-additional .block-blog-posts li:hover a, .sidebar-additional .block-blog-posts li:hover:before, .sidebar-additional .block-blog-categories li:hover:before, .blog-view .post-actions .prev-action:hover a, .blog-view .post-actions .next-action:hover a, .blog-view .post-actions .prev-action:hover i, .blog-view .post-actions .next-action:hover i, .header-area .horizontal-menu .mgs-megamenu--main ul li:hover > a, .header-area .horizontal-menu .nav-main-menu .dropdown-mega-menu .mega-menu-content .menu-col li:hover > a, .header-area .horizontal-menu .nav-main-menu .dropdown-mega-menu .dropdown-submenu-ct li:hover > a, .header-area .vertical-menu > li:hover > a, .header-area .vertical-menu > li.item-lead:hover > a, .header-area .horizontal-menu .mgs-megamenu--main .mega-menu-content .dropdown-menu-ct li:hover > a, .page-header .header-area .customer-web-config .switcher .action:hover,  .header-area .horizontal-menu .mgs-megamenu--main .dropdown-mega-menu .mega-menu-content .dropdown-menu-ct li:hover > a, .header-area .horizontal-menu .mgs-megamenu--main ul li:hover > a, .header-area .horizontal-menu .mgs-megamenu--main ul li.active > a, .menu-banner:hover .title, .header-area.header2 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, .vertical-menu .category-menu .dropdown-menu .sub-menu .mega-menu-sub-title, .page-header .header-area:not(.header6) .block-search .action.search:hover, .btn.btn-link , .btn.btn-default, .action.btn-default, .page-header .header2 .top-header .conts-top-header .customer-web-config .switcher .action:hover, .page-header .header2 .top-header .conts-top-header a:hover, .product-info-main .product-addto-links a.btn-product, .product-options-bottom .product-addto-links a.btn-product, .btn.btn-spinner:hover, .btn.btn-spinner:focus , .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, .page-header .header9 .top-header .conts-top-header .customer-web-config .switcher .action:hover, .page-header .header9 .top-header .conts-top-header a:hover {color:'.$mainLinkHoverColor.'}';
				
				$html .= '.blog-post-item .post-aux a:hover, .footer4 .social-icon:hover, .footer4 .social-icon:focus, .sidebar-additional .block-blog-tags .block-content .tag-cloud li:hover, .form-control:focus, .btn.btn-default, .action.btn-default , .page-header .header6 .block-search .block-content input, .page-header .header-area .minicart-wrapper .minicart-items .product-item > .product .product-item-details .details-qty .update-cart-item , header.page-header .search-mobile .block-search .action.search, .action.btn-default:hover, .btn.btn-default._hover, .action.btn-default._hover, .btn.btn-default:focus, .action.btn-default:focus, .btn.btn-default-dark:hover , .action.btn-default-dark:hover , .bg-primary-hover:hover {border-color:'.$mainLinkHoverColor.'}';

				$html .= '.sidebar-additional .block-blog-categories .block-content a:after, .footer3 .widget-list a:before, .header-area.header1 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .header-area.header1 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, .page-header .header-area .minicart-wrapper .action.showcart .counter.qty, .tip, .header-area .category-dropdown:hover .title-vertical-menu, .header-area .category-dropdown:focus .title-vertical-menu, .page-header .header3.header-area .horizontal-menu .mgs-megamenu--main > ul > li > a::before , .compare-header .block-compare .block-title .counter.qty , .header-area .category-dropdown.category-market.active-cate .title-vertical-menu, .header-area .category-dropdown.category-market.active-cate-sticky .title-vertical-menu ,.page-header .header6 .block-search .block-content .action.search , .header-area.header6 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before , .header-area .category-dropdown:not(.category-market):hover .title-vertical-menu, .header-area .category-dropdown:not(.category-market):focus .title-vertical-menu , .page-header .header-area .compare-header .block-compare .block-title .counter.qty , .page-header .header-area .compare-header .block-compare .block-title .counter.qty, .page-header .header-area:not(.header5) .top-wishlist .action .counter.qty, .header-area.header2 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .btn.btn-primary, .page-header .header-area .minicart-wrapper .minicart-items .product-item > .product .product-item-details .details-qty .update-cart-item , header.page-header .search-mobile .block-search .action.search, .btn.btn-default:hover, .action.btn-default:hover, .btn.btn-default._hover, .action.btn-default._hover, .btn.btn-default:focus, .action.btn-default:focus, .header-area.header2 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .header-area.header2 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, .header-area.header6 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .header-area.header6 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before, .landing-categories .info-subcate .btn-cate-link , .page-header .mobile-menu-wrapper .nav.nav-tabs .nav-item .nav-link:before , .single-deal .product-item-info.template-1 .product-item-details .deal-timer > div > span , .btn.btn-default-dark:hover , .action.btn-default-dark:hover, .btn-product-gallery:hover, .btn-product-gallery:focus, .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before  {background-color:'.$mainLinkHoverColor.'}';
				
				$html .= '.btn.btn-link:hover, .product.info.detailed .product.data.items .item.title.active > a.switch, .product.info.detailed .product.data.items .item.title > .switch:hover, .product.info.detailed .product.data.items .item.title > .switch:focus {border-bottom-color:'.$mainLinkHoverColor.'}';
				$html .= '.btn.btn-default:hover, .action.btn-default:hover, .btn.btn-default._hover, .action.btn-default._hover, .btn.btn-default:focus, .action.btn-default:focus {color: #fff}';
				$html .= '.portfolio-category-view a:hover {color: ' . $mainLinkHoverColor .' !important;}';
				$html .= '.locator-index-index .store-list-container .store-list .store-list-items .action-toolbar .pager .pages-items .item.pages-item-next .action:hover , .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li:hover > a:before, .header-area.header9 .horizontal-menu .mgs-megamenu--main > ul > li.active > a:before  {background-color: '. $mainLinkHoverColor .' !important}';
				$html .= '.btn.btn-default:hover, .action.btn-default:hover, .btn.btn-default._hover, .action.btn-default._hover, .btn.btn-default:focus, .action.btn-default:focus, .btn.primary.btn-default, .action.primary.btn-default , .btn.btn-default-dark:hover , .action.btn-default-dark:hover, .btn-product-gallery:hover, .btn-product-gallery:focus {color: #fff !important}';
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
				$html .= 'btn.primary, .action.primary, .btn.action.primary, .btn.btn-primary {color:'.$primaryButtonTextColor.' !important}';
			}
			/* Text hover color */
			$primaryButtonTextHoverColor = $this->getStoreConfig('themesettings/main/primary_button_text_hover_color', $storeId);
			if($primaryButtonTextHoverColor!=''){
				$html .= 'btn.primary:hover, .action.primary:hover, .btn.action.primary:hover , .btn.btn-primary:hover  {color:'.$primaryButtonTextHoverColor.' !important}';
			}
			
			/* Background color */
			$primaryButtonBackgroundColor = $this->getStoreConfig('themesettings/main/primary_button_background_color', $storeId);
			if($primaryButtonBackgroundColor!=''){
				$html .= 'btn.primary, .action.primary, .btn.action.primary, .btn.btn-primary  {background-color:'.$primaryButtonBackgroundColor.' !important}';
			}
			/* Background hover color */
			$primaryButtonBackgroundHoverColor = $this->getStoreConfig('themesettings/main/primary_button_background_hover_color', $storeId);
			if($primaryButtonBackgroundHoverColor!=''){
				$html .= 'btn.primary:hover, .action.primary:hover , .btn.action.primary:hover, .btn.btn-primary:hover {background-color:'.$primaryButtonBackgroundHoverColor.' !important}';
				
				$html .= '.btn-dark:hover{background-color:'.$primaryButtonBackgroundHoverColor.'; border-color:'.$primaryButtonBackgroundHoverColor.' !important}';
			}
			
			/* Border color */
			$primaryButtonBorderColor = $this->getStoreConfig('themesettings/main/primary_button_border_color', $storeId);
			if($primaryButtonBorderColor!=''){
				$html .= 'btn.primary, .action.primary, .btn.action.primary, .btn.btn-primary  {border-color:'.$primaryButtonBorderColor.' !important}';
			}
			/* Border hover color */
			$primaryButtonBorderHoverColor = $this->getStoreConfig('themesettings/main/primary_button_border_hover_color', $storeId);
			if($primaryButtonBorderHoverColor!=''){
				$html .= 'btn.primary:hover, .action.primary:hover, .btn.action.primary:hover, .btn.btn-primary:hover  {border-color:'.$primaryButtonBorderHoverColor.' !important}';
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
			if($middleFooterTextColor!=''){
				$html .= 'footer.page-footer .middle-footer p {color:'.$middleFooterTextColor.';}';
			}
			if($middleFooterBackgroundColor!=''){
				$html .= 'footer.page-footer .middle-footer .opening-hours-block .list-opening-hours-info li > span {background-color:'.$middleFooterBackgroundColor.';}';
			}
			if($middleFooterTextColor!=''){
				$html .= 'footer.page-footer .middle-footer .opening-hours-block .list-opening-hours-info li .dotted_line {border-top-color:'.$middleFooterTextColor.';}';
			}
			
			
			/* Link color */
			$middleFooterLinkColor = $this->getStoreConfig('themesettings/footer/footer_middle_link_color', $storeId);
			$middleFooterLinkHoverColor = $this->getStoreConfig('themesettings/footer/footer_middle_link_hover_color', $storeId);
			if($middleFooterLinkColor!=''){
				$html .= 'footer.page-footer .middle-footer a{color:'.$middleFooterLinkColor.';}';
			}
			if($middleFooterLinkHoverColor!=''){
				$html .= 'footer.page-footer .middle-footer a:hover{color:'.$middleFooterLinkHoverColor.';}';
				$html .= 'footer.page-footer .middle-footer .block-social ul li:hover {border-color :'.$middleFooterLinkHoverColor.';}';
				$html .= 'footer.page-footer .middle-footer .block-social ul li:hover a, footer.page-footer .middle-footer .block-social ul li:hover i, .footer2 .middle-footer .widget-about-info a, .footer1 .middle-footer .widget-about-info a {color :'.$middleFooterLinkHoverColor.';}';
				$html .= 'footer.page-footer .middle-footer .logo__social-block .block-social .list-social li:hover {border-color:'.$middleFooterLinkHoverColor.';}';
				$html .= 'footer.page-footer .middle-footer .block-social ul li:hover i {color:'.$middleFooterLinkHoverColor.';}';
				$html .= 'footer.page-footer .middle-footer .middle-content-top .newsletter__social-block .social-block ul li:hover {background-color:'.$middleFooterLinkHoverColor.';}';
				$html .= 'footer.page-footer .middle-footer .middle-content-top .newsletter__social-block .social-block ul li:after {box-shadow: 0 0 0 2px '.$middleFooterLinkHoverColor.';}';
				
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
			if($bottomFooterTextColor!=''){
				$html .= 'footer.page-footer .bottom-footer p {color:'.$bottomFooterTextColor.';}';
			}
			
			/* Link color */
			$bottomFooterLinkColor = $this->getStoreConfig('themesettings/footer/footer_bottom_link_color', $storeId);
			$bottomFooterLinkHoverColor = $this->getStoreConfig('themesettings/footer/footer_bottom_link_hover_color', $storeId);
			if($bottomFooterLinkColor!=''){
				$html .= 'footer.page-footer .bottom-footer a{color:'.$bottomFooterLinkColor.';}';
				$html .= 'footer.page-footer .bottom-footer .block-link-bottom .block-link-bottom-footer li:not(:last-child):after{background:'.$bottomFooterLinkColor.';}';
			}
			if($bottomFooterLinkHoverColor!=''){
				$html .= 'footer.page-footer .bottom-footer a:hover{color:'.$bottomFooterLinkHoverColor.';}';
			}
			
			/* Icon color */
			$bottomFooterIconColor = $this->getStoreConfig('themesettings/footer/footer_bottom_icon_color', $storeId);
			if($bottomFooterIconColor!=''){
				$html .= 'footer.page-footer .bottom-footer .theme-footer-icon, footer.page-footer .bottom-footer .social-icon i {color:'.$bottomFooterIconColor.';}';
			}
		}
		
		return $html;
	}
	public function getCurrentCategory(){
		$id = $this->_request->getParam('id');
		$this->_currentCategory = $this->getModel('Magento\Catalog\Model\Category')->load($id);
		return $this->_currentCategory;
	}
	
	public function getCate($product){
		$categories = $product->getCategoryIds();
		$html = '';
		if(count($categories)>0){
			foreach($categories as $_categoryId){
				$category = $this->categoryRepository->get($_categoryId);
				$html .= '<a href="'.$category->getUrl().'" class="category-link cate-name">'.$category->getName().'</a>, ';
			}
			$html = substr($html, 0, -2);
		}
		return $html;
	}
	
	public function getImageUrl($image)
    {
        $url = false;
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
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
	
	public function getMediaGalleryImages($product)
    {
        $_product = $this->productRepository->get($product->getSku(), false, null, true);

        return $_product->getMediaGalleryImages();
    }
    public function getCurrentDateTime(){
		$now = $this->_date->gmtDate();
		return $now;
	}

	public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');        
        
        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
                
        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }
        
        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
        
        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize); 
        }    
        
        return $collection;
    }
	
	public function generateCssCustomWidth($customWidth){
		return '.custom .navigation, .custom .breadcrumbs, .custom .page-header .header.panel, .custom .header.content, .custom .footer.content, .custom .page-wrapper > .widget, .custom .page-wrapper > .page-bottom, .custom .block.category.event, .custom .top-container, .custom .page-main{max-width: '. $customWidth .'px;margin: 0 auto; padding: 0 10px;}';
	}

	public function createObjBlock($pathToBlock){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$obj = $objectManager->create($pathToBlock);
		return $obj;
	}
}