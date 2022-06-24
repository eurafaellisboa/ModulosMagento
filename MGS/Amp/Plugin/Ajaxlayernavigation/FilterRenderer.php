<?php
namespace MGS\Amp\Plugin\Ajaxlayernavigation;

use MGS\Ajaxlayernavigation\Block\Navigation\RenderLayered;

class FilterRenderer {
    /**
     * @var \MGS\Amp\Helper\Config
     */
    protected $_configHelper;

	/**
     * Path to AMP-template file.
     *
     * @var string
     */
    protected $_template = 'MGS_Amp::MGS_Ajaxlayernavigation/product/layered/renderer.phtml';
	
    /**
     * @param \MGS\Amp\Helper\Config $configHelper
     * @return  void
     */
    public function __construct(
        \MGS\Amp\Helper\Config $configHelper
    ) {
        $this->_configHelper = $configHelper;
    }
	
	/**
     * @param  RenderLayered
     * @param  string $result
     * @return string $result
     */
    public function afterGetTemplate(RenderLayered $subject, $result)
    {
        if (!$this->_configHelper->isAmpCall()){
            return $result;
        }

        return $this->_template;
    }

}
