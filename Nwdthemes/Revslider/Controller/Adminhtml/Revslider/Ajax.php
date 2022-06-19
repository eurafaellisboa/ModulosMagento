<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml\Revslider;

use \Nwdthemes\Revslider\Model\Revslider\Admin\RevSliderAdmin;

class Ajax extends \Nwdthemes\Revslider\Controller\Adminhtml\Revslider {

    protected $_request;

    /**
     * Constructor
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper
    ) {
        $this->_request = $context->getRequest();

        parent::__construct($context, $frameworkHelper, $pluginHelper);
    }

    /**
     * Ajax action
     */

    public function execute() {
        $this->_frameworkHelper->reGeneratePluginPath();
        $revSliderAdmin = new RevSliderAdmin($this->_frameworkHelper);
        $action = $this->_request->getParam('action');
        if ($action && $action !== 'revslider_ajax_action') {
            echo $this->_frameworkHelper->do_action('wp_ajax_' . $action);
        } else {
            $revSliderAdmin->do_ajax_action();
        }
    }

}
