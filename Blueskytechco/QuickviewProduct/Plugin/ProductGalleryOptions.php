<?php
namespace Blueskytechco\QuickviewProduct\Plugin;

use Blueskytechco\QuickviewProduct\Helper\Data as Helper;

class ProductGalleryOptions
{
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     *
     * @var  \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     *
     * @var  \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;
    
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        Helper $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->helper = $helper;
    }

    public function afterGetOptionsJson(
        \Magento\Catalog\Block\Product\View\GalleryOptions $subject,
        $result
    ) {
        if ($this->request->getFullActionName() != 'blueskytechco_quickview_product_view') {
            return $result;
        }
        $nav = $this->helper->getData('quickview_product/general/nav');
        $navdir = $this->helper->getData('quickview_product/general/navdir');
        
        $navdir = ($navdir == 'top' || $navdir == 'bottom')?'horizontal':
            'vertical';
        $result = $this->jsonDecoder->decode($result);
        $result['nav'] = $nav;
        $result['navdir'] = $navdir;
        $result['allowfullscreen'] = false;

        return $this->jsonEncoder->encode($result);
    }
}
