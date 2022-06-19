<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml\Revslider;

class Addonajax extends \Nwdthemes\Revslider\Controller\Adminhtml\Revslider {

    protected $_resultLayoutFactory;

    /**
     * Constructor
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper
    ) {
        $this->_resultLayoutFactory = $resultLayoutFactory;

        parent::__construct($context, $frameworkHelper, $pluginHelper);
    }

    /**
     * Ajax action
     */

    public function execute() {
        $resultLayout = $this->_resultLayoutFactory->create();
        return $resultLayout;
    }

}
