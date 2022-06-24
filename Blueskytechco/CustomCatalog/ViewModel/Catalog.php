<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 
namespace Blueskytechco\CustomCatalog\ViewModel;

use Blueskytechco\CustomCatalog\Helper\Data as Helper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Catalog implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectmanager;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Blueskytechco\WidgetCategory\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    
    /**
     * @var GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Blueskytechco\WidgetCategory\Model\ImageUploader $imageUploader,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        PriceCurrencyInterface $priceCurrency,
        \Magento\CatalogInventory\Api\StockStateInterface $_stockState,
        \Magento\Catalog\Helper\Image $imageHelper,
        Registry $registry,
        Helper $helper
    ) {
        $this->stockState = $_stockState;
        $this->storeManager = $storeManager;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->request = $request;
        $this->objectManager = $objectmanager;
        $this->categoryRepository = $categoryRepository;
        $this->imageUploader = $imageUploader;
        $this->helper = $helper;
        $this->priceCurrency = $priceCurrency;
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
    }
    
    /**
     * return data config Admin
     */
    public function getDataQuickView($config)
    {
        $config = $this->helper->getData('quickview_product/general/'.$config);
        return $config;
    }

    /**
     * return params url
     */
    public function getParams()
    {
        $params = $this->request->getParams();
        return $params;
    }

    /**
     * return params buy now
     */
    public function getParamsBuyNow()
    {
        $params = $this->request->getParams();
        $buy_now = '';
        if($this->helper->getData('themesetting/product/enable_buy_now_button') == 'enable'){
            $buy_now = 'buy-now';
        }
        if(isset($params['cart']) && $params['cart'] == 'buy-now'){
            $buy_now = $params['cart'];
        }
        return $buy_now;
    }

    /**
     * return params sticky
     */
    public function getParamsProductSticky()
    {
        $params = $this->request->getParams();
        $sticky = (isset($params['sticky']) && $params['sticky'] == 'enable')
            ? $params['sticky']
            : '';
        if($this->helper->getData('themesetting/product/sticky_add_to_cart') == 'enable'){
            $sticky = 'enable';
        }
        
        return $sticky;
    }

    /**
     * return params sticky
     */
    public function getParamsProductBrand()
    {
        $params = $this->request->getParams();
        $sticky = (isset($params['brand']) && $params['brand'] == 'enable')
            ? $params['brand']
            : '';
        return $sticky;
    }

    /**
     * return sub category html
     */
    public function getSubCategoryHtml()
    {
        $params = $this->getParams();
        $view_type = '';
        if($this->helper->getData('themesetting/category/enable_sub_cat')){
            $view_type = 'sub';
        }
        if(isset($params['view-type']) && $params['view-type'] == 'sub'){
            $view_type = 'sub';
        }
        $html = '';
        $category = $this->registry->registry('current_category');
        if (!$category || $view_type !== 'sub') {
            return $html;
        }
        $catId = $category->getId();
        $category = $this->categoryRepository->get($catId);
        $subCats = $category->getChildrenCategories();
        if (count($subCats) == 0) {
            return $html;
        }
        
        $html .= '<div class="sub-category">';
            $html .= '<div class="sub-category-slide slick-slider">';
            foreach ($subCats as $subcat) {
                $sub_category = $this->categoryRepository->get($subcat->getId());
                $subcaturl = $sub_category->getUrl();
                $_imgUrl = '';
                if ($sub_category->getData('widget_category_thumbnail')) {
                    $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
                    $_imgUrl = $mediaUrl.$this->imageUploader->getBasePath().'/'.$sub_category->getData('widget_category_thumbnail');
                }
                $html .= '<div class="col-cat">';
                    $html .= '<div class="mf-image-box">';
                        if ($_imgUrl) {
                            $html .= '<img src="'.$_imgUrl.'" alt="'.$sub_category->getName().'" />';
                        }
                        $html .= '<div class="image-content">';
                            $html .= '<a href="'.$subcaturl.'" class="box-title">'.$sub_category->getName().'</a>';
                        $html .= '</div> ';
                    $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public function getStockQty($productId, $websiteId = null)
    {
        return $this->stockState->getStockQty($productId, $websiteId);
    }

    public function getProductSalableQuantity($pro)
    {
        $sale_qty = 0;
        if($pro->getTypeId() == 'simple' || $pro->getTypeId() == 'virtual'){
            $qty = $this->getStockQty($pro->getId());
            $salableqty = $this->getSalableQuantityDataBySku->execute($pro->getSku());
            if($qty && $qty > 0){
                $sale_qty = isset($salableqty[0]['qty']) ? $salableqty[0]['qty'] : 0;
                return $sale_qty;
            }
        }
        return $sale_qty;
    }

    public function getFormatedPrice($price)
    {
        return $this->priceCurrency->convertAndFormat($price);
    }

    public function getActionZoom()
    {
        $params = $this->request->getParams();
        $zoom = '';
        if($this->helper->getData('themesetting/product/images_zoom') != ''){
            $zoom = $this->helper->getData('themesetting/product/images_zoom');
        }
        if(isset($params['zoom']) && $params['zoom']){
            $zoom = $params['zoom'];
        }
        return $zoom;
    }

    public function getThumbImage()
    {
        $params = $this->request->getParams();
        $thumb = '';
        if($this->helper->getData('themesetting/product/display_thumbnails') != ''){
            $thumb = $this->helper->getData('themesetting/product/display_thumbnails');
        }
        if(isset($params['thumb']) && $params['thumb']){
            $thumb = $params['thumb'];
        }
        return $thumb;
    }

    public function getGalleryType()
    {
        $params = $this->request->getParams();
        $gallery_type = '';
        if($this->helper->getData('themesetting/product/enable_photoswipe_popup') == 'enable'){
            $gallery_type = 'pswp';
        }
        if(isset($params['gallery-type']) && $params['gallery-type']){
            $gallery_type = $params['gallery-type'];
        }
        return $gallery_type;
    }

    public function getImageHelper()
    {
        return $this->imageHelper;
    }

    public function getVideoUrl($url)
    {
        if (strpos($url, 'youtube') !== false) {
            $url_array = explode('v=',$url);
            if(isset($url_array[1]) && $url_array[1]){
                $id = explode('&', $url_array[1]);
                if (isset($id[0]) && $id[0]) {
                    $url = 'https://www.youtube.com/embed/'.$id[0].'';
                    return $url;
                }
            }
        }
        return false;
    }
}
