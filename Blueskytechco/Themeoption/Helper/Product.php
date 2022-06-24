<?php
namespace Blueskytechco\Themeoption\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{	
	protected $_productRepository;
	protected $_imageHelper;
    protected $storeManager;
    protected $categoryFactory;
    protected $_registry;
    protected $_filterProvider;
    protected $_blockFactory;

    private $main_image_width;
    private $other_image_width;

	public function __construct(
		\Magento\Framework\Registry $registry,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
    	$this->_filterProvider = $filterProvider;
        $this->_blockFactory = $blockFactory;
    	$this->_registry = $registry;
    	$this->_productRepository = $productRepository;
        $this->_categoryFactory = $categoryFactory;
    	$this->imageHelper = $imageFactory;
        $this->storeManager = $storeManager;
        $this->main_image_width = ['product_page_image_large','product_page_image_medium','product_page_main_image','product_page_main_image_default'];
        $this->other_image_width = ['bundled_product_customization_page','mini_cart_product_thumbnail','product_comparison_list','product_page_image_small','product_swatch_image_small','product_page_more_views','product_small_image','product_thumbnail_image','new_products_images_only_widget','customer_account_my_tags_tag_view','customer_shared_wishlist','gift_messages_checkout_small_image','gift_messages_checkout_thumbnail','product_stock_alert_email_product_image','recently_compared_products_images_names_widget','recently_compared_products_images_only_widget','recently_viewed_products_images_names_widget','recently_viewed_products_images_only_widget','rss_thumbnail','sendfriend_small_image','shared_wishlist_email','side_column_widget_product_thumbnail','wishlist_sidebar_block','wishlist_small_image'];
        parent::__construct($context);
    }

    public function getProductCustomTab()
    {
    	$c_tabs = [];
    	$store_id = $this->storeManager->getStore()->getId();
    	$current_product = $this->_registry->registry('current_product');
    	if($current_product){
    		$custom_tabs = $this->getConfiguration('themesetting/product/custom_tabs');
    		if($custom_tabs){
    			$custom_tabs_unserialize = unserialize($custom_tabs);
    			if(is_array($custom_tabs_unserialize) && !empty($custom_tabs_unserialize)){
    				foreach ($custom_tabs_unserialize as $key_tab => $val_tab) {
    					if(isset($val_tab['tabs_title']) && $val_tab['tabs_title'] != '' && isset($val_tab['tabs_code']) && $val_tab['tabs_code'] != ''){
    						$sort_order = (!$val_tab['sort_order'] || !is_numeric($val_tab['sort_order'])) ? 0 : $val_tab['sort_order'];
    						$content = '';
    						if($val_tab['tabs_type'] == 'blocks'){
    							$block = $this->_blockFactory->create();
                    			$block->setStoreId($store_id)->load($val_tab['tabs_code']);
                    			if($block){
                    				$content = ($block->getContent()) ? $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($block->getContent()) : '';
                    			}
    						}
    						else{
    							$attribute = $current_product->getResource()->getAttribute($val_tab['tabs_code']);
    							if($attribute){
    								$attr_value = $attribute->getFrontend()->getValue($current_product);
    								$content = ($attr_value) ? $this->_filterProvider->getBlockFilter()->setStoreId($store_id)->filter($attr_value) : '';
    							}
    						}
    						$c_tabs[] = ['id' => 'custom-product-tab-'.$val_tab['tabs_code'], 'title' => $val_tab['tabs_title'], 'content' => $content, 'sort_order' => $sort_order];
    					}
    				}
    			}
    		}
    	}
    	if(count($c_tabs) > 0){
            $c_tabs = $this->customSort($c_tabs);
    	}

    	return $c_tabs;
    }

    public function customSort($tabs) {
    	$arr_1 = [];
        foreach($tabs as $k_t => $val_t) {
            $arr_1[$k_t] = $val_t['sort_order'];
        }
        array_multisort($arr_1, SORT_ASC, $tabs);
        return $tabs;
    }

    public function getCustomClassStyleProduct()
    {
    	$c = $this->getConfiguration('themesetting/category/product_style');
    	return $c;
    }

    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getImageUrlLoading()
    {
    	return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    }

    public function getConfigurationSizeImages($img, $process_result, $storeId = null)
	{
		$type = 'small_image_width';
		$width_image = $this->getConfiguration('themesetting/product/'.$type, $storeId) ? $this->getConfiguration('themesetting/product/'.$type, $storeId) : 500;
		if(in_array($img, $this->main_image_width)){
			$type = 'main_image_width';
			$width_image = $this->getConfiguration('themesetting/product/'.$type, $storeId) ? $this->getConfiguration('themesetting/product/'.$type, $storeId) : 500;
		}
		elseif(in_array($img, $this->other_image_width)){
			$width_image = $process_result['width'];
		}
		
		$width_image = intval($width_image);
		$cropping = $this->getConfiguration('themesetting/product/cropping', $storeId);
		$cropping_width = floatval($this->getConfiguration('themesetting/product/cropping_width', $storeId));
		$cropping_height = floatval($this->getConfiguration('themesetting/product/cropping_height', $storeId));
		$height_image = $width_image;
		if($cropping == 'custom'){
			if($cropping_width > 0 && $cropping_height > 0){
				$w = $cropping_width;
				$h = $cropping_height;
				$n_h = $h / $w;
				$new_height = $n_h * $width_image;
				$height_image = intval($new_height);
			}
		}
		
		$process_result['width'] = $width_image;
		$process_result['height'] = $height_image;
		return $process_result;
	}

    public function getConfiguration($path, $storeId = null)
	{
		return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
	}

    public function loadCategoryById($id)
	{
		return $this->_categoryFactory->create()->load($id);
	}

    public function getRootCategoryId()
	{
		return $this->storeManager->getStore($this->storeManager->getStore()->getId())->getRootCategoryId();
	}

	public function getClassImagehovereffects()
	{
		return $this->getConfiguration('themesetting/product/image_hover_effects');
	}

	public function enableImageHover()
	{
		if($this->getConfiguration('themesetting/product/enable_hover_image') == 'enable'){
			return true;
		}
		return false;
	}
	
	public function getProductById($id)
	{
		return $this->_productRepository->getById($id);
	}
	
	public function getProductBySku($sku)
	{
		return $this->_productRepository->get($sku);
	}

	public function resizeImage($product, $typeImage, $width, $height)
    {
        $imageHelper = $this->imageHelper->create();
        $imageHelper->init($product, $typeImage)->setImageFile($product->getData($typeImage))->resize($width, $height);
        return $imageHelper->getUrl();
    }

    public function getProductNewLabel($_product)
    {
    	if($this->getConfiguration('themesetting/product/show_new') == 'enable'){
	        $newFromDate = $_product->getNewsFromDate();
	        $newToDate = $_product->getNewsToDate();                 
			$now = date("Y-m-d H:m(worry)");
			$label = $this->getConfiguration('themesetting/product/show_new_text') ? $this->getConfiguration('themesetting/product/show_new_text') : __('NEW');
			if($newFromDate && $newToDate && $newFromDate <= $now && $newToDate >= $now) {
				return '<span class="newlabel label-product">'.$label.'</span>';
			}
		}
		return '';
    }

    public function getProductSaleLabel($_product)
    {
    	if($this->getConfiguration('themesetting/product/show_sale') == 'enable'){
	        $specialprice = $_product->getSpecialPrice();
	        $specialPriceFromDate = $_product->getSpecialFromDate();
	        $specialPriceToDate = $_product->getSpecialToDate();
	        $now = date("Y-m-d H:m(worry)");
	        $html = '';

	        if($_product->getTypeId() == 'simple' || $_product->getTypeId() == 'virtual' || $_product->getTypeId() == 'downloadable'){
				if($specialprice && $specialPriceFromDate && $specialPriceToDate && $specialPriceFromDate <= $now && $specialPriceToDate >= $now) {
					$price = $_product->getPrice();
					$price_final = $_product->getPriceInfo()->getPrice('final_price')->getValue();
					if($price && $price_final && $price_final < $price){
						$price = (float)$price;
						$price_final = (float)$price_final;
						$sale = $price - $price_final;
						$pec = ($sale / $price) * 100;
						$text_label = $this->getConfiguration('themesetting/product/show_sale_text') ? $this->getConfiguration('themesetting/product/show_sale_text') : __('SALE');
						$label = $this->getConfiguration('themesetting/product/sale_discount_percent') ? round($pec).'%' : $text_label;
						$html = '<span class="onsale label-product">'.$label.'</span>';
					}
				}
			}
			return $html;
		}
		return '';
    }
}
?>