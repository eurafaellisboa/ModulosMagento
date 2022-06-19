<?php

namespace Nwdthemes\Revslider\Block;

use \Nwdthemes\Revslider\Model\Revslider\Front\RevSliderFront;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderCssParser;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderFunctions;

class Head extends \Magento\Framework\View\Element\Template {

	protected $_frameworkHelper;

	/**
	 * Constructor
	 */

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Nwdthemes\Revslider\Helper\Framework $frameworkHelper
	) {
		$this->_frameworkHelper = $frameworkHelper;
        parent::__construct($context);
	}

	/**
	 * Get head includes
	 *
	 * @return string
	 */
	public function getHeadIncludes() {
        $output = '';

        // output head includes
        new RevSliderFront($this->_frameworkHelper);
        ob_start();
        $this->_frameworkHelper->do_action('wp_enqueue_scripts');
        $this->_frameworkHelper->do_action('wp_head');
        $output .= ob_get_contents();
        ob_clean();
        ob_end_clean();

        // output static styles
        $revSliderCssParser = new RevSliderCssParser($this->_frameworkHelper);
        $revSliderFunctions = new RevSliderFunctions($this->_frameworkHelper);
        if ($staticCss = $revSliderCssParser->compress_css($revSliderFunctions->get_static_css())) {
            $output .= '<style type="text/css">' . $staticCss . '</style>';
        }

        $output .= $this->_frameworkHelper->getLocalizeScriptsHtml();

        return $output;
	}

}
