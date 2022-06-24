<?php
namespace Blueskytechco\DailyDeal\Block\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Rule\Model\Condition\Combine;
use Magento\Rule\Model\Condition\Sql\Builder as SqlBuilder;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
 
class DailyDeal extends AbstractProduct implements BlockInterface, IdentityInterface
{
    protected $_templateFilterContent;
    protected $httpContext;
    protected $_date;
    protected $catalogProductVisibility;
    protected $productCollectionFactory;
    protected $sqlBuilder;
    private $priceCurrency;
    private $urlEncoder;
    private $layoutFactory;
    private $categoryRepository;
    private $json;
    private $getSalableQuantityDataBySku;
    protected $stockState;

    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        EncoderInterface $urlEncoder = null,
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        HttpContext $httpContext,
        LayoutFactory $layoutFactory = null,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\CatalogInventory\Api\StockStateInterface $_stockState,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        array $data = [],
        Json $json = null,
        CategoryRepositoryInterface $categoryRepository = null
    ) {
        $this->_templateFilterContent = $filterProvider;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->urlEncoder = $urlEncoder ?: ObjectManager::getInstance()->get(EncoderInterface::class);
        $this->layoutFactory = $layoutFactory ?: ObjectManager::getInstance()->get(LayoutFactory::class);
        $this->_date = $date;
        $this->stockState = $_stockState;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->categoryRepository = $categoryRepository ?? ObjectManager::getInstance()->get(CategoryRepositoryInterface::class);
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [
                Product::CACHE_TAG,
            ],
        ]);
    }

    public function getCacheKeyInfo()
    {
        $conditions = md5($this->getDataWidgetConfig('category_id'));

        return [
            'CATALOG_PRODUCTS_DAILYDEAL_WIDGET',
            $this->getPriceCurrency()->getCurrency()->getCode(),
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            (int)$this->getDataWidgetConfig('number_products'),
            $this->json->serialize($this->getRequest()->getParams()),
            $conditions,
            $this->getTemplate(),
            $this->getTitle()
        ];
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = $priceRender->render(
            FinalPrice::PRICE_CODE,
            $product,
            $arguments
        );

        return $this->formatProductPriceHtml($product, $price);
    }

    public function formatProductPriceHtml(Product $product, $price){
        $order = array(' id="'.$product->getId().'"',' id="product-price-'.$product->getId().'"', ' id="old-price-'.$product->getId().'"');
        $replace = '';
        return str_replace($order, $replace, $price);
    }

    protected function getDetailsRendererList()
    {
        if (empty($this->rendererListBlock)) {
            /** @var $layout LayoutInterface */
            $layout = $this->layoutFactory->create(['cacheable' => false]);
            $layout->getUpdate()->addHandle('catalog_widget_product_list')->load();
            $layout->generateXml();
            $layout->generateElements();

            $this->rendererListBlock = $layout->getBlock('category.product.type.widget.details.renderers');
        }
        return $this->rendererListBlock;
    }

    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlEncoder->encode($url),
            ]
        ];
    }

    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->createCollection());
        return parent::_beforeToHtml();
    }

    public function getCol()
    {
        $col_xxl = $this->getDataWidgetConfig('col_xxl');
        $col_xl = $this->getDataWidgetConfig('col_xl');
        $col_lg = $this->getDataWidgetConfig('col_lg');
        $col_md = $this->getDataWidgetConfig('col_md');
        $col_sm = $this->getDataWidgetConfig('col_sm');
        $col_xs = $this->getDataWidgetConfig('col_xs');
        return ' col-xxl-'.$col_xxl.' col-xl-'.$col_xl.' col-lg-'.$col_lg.' col-md-'.$col_md.' col-sm-'.$col_sm.' col-'.$col_xs;
    }

    public function _toHtml()
    {
        $html = parent::_toHtml();
        return $html;
    }

    public function getDataCountdown($pro)
    {
        if($pro->getSpecialToDate() && $pro->getSpecialToDate() != ''){
            return date('Y/m/d 23:59:59', strtotime($pro->getSpecialToDate()));
        }
        return '';
    }

    public function getStockQty($productId, $websiteId = null)
    {
        return $this->stockState->getStockQty($productId, $websiteId);
    }

    public function getProductSalableQuantityHtml($pro)
    {
        $html = '';
        if($pro->getTypeId() == 'simple' || $pro->getTypeId() == 'virtual'){
            $qty = $this->getStockQty($pro->getId());
            $salableqty = $this->getSalableQuantityDataBySku->execute($pro->getSku());
            if($qty && $qty > 0){
                $sale_qty = isset($salableqty[0]['qty']) ? $salableqty[0]['qty'] : 0;
                $sold = $qty - $sale_qty;
                $percent = ($sold / $qty) * 100;
                $html .= '<div class="container-sold-salable-quantity">';
                    $html .= '<div class="container-sold-percent"><div class="sold-percent" style="width: '.$percent.'%;"></div></div>';
                    $html .= '<div class="container-sold-number"><span class="text-sold">'.__('Sold:').'</span><span class="number-sold">'.$sold.'/'.$qty.'</span></div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    public function createCollection()
    {
        $collection = $this->productCollectionFactory->create();
        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)->addStoreFilter();
        $category_ids = $this->updateAnchorCategoryConditions($this->getDataWidgetConfig('category_id'));
        $collection->addCategoriesFilter(array('in' => $category_ids));
        if($this->getDataWidgetConfig('number_products') != ''){
            $collection->setPageSize($this->getDataWidgetConfig('number_products'));
        }
        $collection->distinct(true);

        return $collection;
    }

    public function getIdentities()
    {
        $identities = [];
        if ($this->getProductCollection()) {
            foreach ($this->getProductCollection() as $product) {
                if ($product instanceof IdentityInterface) {
                    $identities[] = $product->getIdentities();
                }
            }
        }
        $identities = array_merge([], ...$identities);

        return $identities ?: [Product::CACHE_TAG];
    }

    public function getCategoriesUrl()
    {
        $url = '';
        try {
            $category = $this->categoryRepository->get($this->getDataWidgetConfig('category_id'), $this->_storeManager->getStore()->getId());
            $url = $category->getUrl();
        } catch (NoSuchEntityException $e) {
            $url = '';
        }
        return $url;
    }

    private function updateAnchorCategoryConditions($categoryId)
    {
        try {
            $category = $this->categoryRepository->get($categoryId, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return [];
        }

        $children = $category->getIsAnchor() ? $category->getChildren(true) : [];
        if ($children) {
            $children = explode(',', $children);
            return array_merge([$categoryId], $children);
        }

        return [$categoryId];
    }

    private function getPriceCurrency()
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = ObjectManager::getInstance()
                ->get(PriceCurrencyInterface::class);
        }
        return $this->priceCurrency;
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        $requestingPageUrl = $this->getRequest()->getParam('requesting_page_url');

        if (!empty($requestingPageUrl)) {
            $additional['useUencPlaceholder'] = true;
            $url = parent::getAddToCartUrl($product, $additional);
            return str_replace('%25uenc%25', $this->urlEncoder->encode($requestingPageUrl), $url);
        }

        return parent::getAddToCartUrl($product, $additional);
    }

    public function getDataWidgetConfig($path)
    {
        return $this->getData($path) ?: '';
    }

    public function filterOutputContent($content)
    {
        $content = (string) $content ?: '';
        if($content != ''){
            $arr_encode = ['^[','^]','`','|','&lt;','&gt;'];
            $arr_decode = ['{','}','"','\\','<','>'];
            $new_content = str_replace($arr_encode, $arr_decode, $content);
            return $this->_templateFilterContent->getPageFilter()->filter(
                (string) $new_content ?: ''
            );
        }
        return '';
    }
}
?>
