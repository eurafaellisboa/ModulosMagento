<?php
namespace Blueskytechco\QuickviewProduct\Plugin\Renderer;

class Configurable
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
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    public function aroundGetVar(
        \Magento\Swatches\Block\Product\Renderer\Configurable $subject,
        \Closure $proceed,
        $name,
        $module = null
    ) {
        $result = $proceed('gallery_switch_strategy', 'Magento_ConfigurableProduct');

        if ($this->request->getFullActionName() != 'blueskytechco_quickview_product_view') {
            return $result;
        }
        
        $result = 'prepend';
        
        return $result;
    }
}
