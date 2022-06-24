<?php
namespace Blueskytechco\QuickviewProduct\Plugin;

class ProductGallery
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
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
    }

    public function afterGetMagnifier(
        \Magento\Catalog\Block\Product\View\Gallery $subject,
        $result
    ) {
        if ($this->request->getFullActionName() != 'blueskytechco_quickview_product_view') {
            return $result;
        }
        $result = $this->jsonDecoder->decode($result);
        $result['enabled'] = false;

        return $this->jsonEncoder->encode($result);
    }
}
