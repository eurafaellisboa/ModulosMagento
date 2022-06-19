<?php

namespace Nwdthemes\Revslider\Block\Adminhtml\Block;

class Template extends \Magento\Backend\Block\Template {

    protected $_framework;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Helper\Data $dataHelper,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
		\Nwdthemes\Revslider\Helper\Register $registerHelper/*,
		\Nwdthemes\Revslider\Model\Revslider\Admin\RevSliderAdmin $revSliderAdmin*/
    ) {
        $this->_framework = $framework;

        parent::__construct($context);

		// TODO: Check what we really need in templates
        $this->assign([
			'frameworkHelper' => $framework,
			'pluginHelper' => $pluginHelper,
			'query' => $query,
			'curl' => $curl,
			'filesystemHelper' => $filesystemHelper,
			'images' => $images,
			'resource' => $resource,
			'googleFonts' => $googleFonts,
            'dataHelper' => $dataHelper,
            'registerHelper' => $registerHelper
		]);
	}

	/**
	 *	Get admin url
	 *
	 *	@param	string	url
	 *	@param	string	args
	 *	@return	string
	 */

	public function getViewUrl($url, $args = '') {
		return $this->_framework->getBackendUrl($url, $args);
	}

}
