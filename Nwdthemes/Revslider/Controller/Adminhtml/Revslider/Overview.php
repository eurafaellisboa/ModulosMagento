<?php

namespace Nwdthemes\Revslider\Controller\Adminhtml\Revslider;

use Nwdthemes\Revslider\Helper\Data;

class Overview extends \Nwdthemes\Revslider\Controller\Adminhtml\Revslider {

    protected $_resultPageFactory;

    /**
     * Constructor
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;

        Data::setPage('revslider');

        parent::__construct($context, $frameworkHelper, $pluginHelper);
    }

    /**
     * Sliders Overview
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

    public function execute() {
        $this->_frameworkHelper->reGeneratePluginPath();
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Nwdthemes_Revslider::overview');
        $resultPage->getConfig()->getTitle()->prepend(__('Slider Overview'));
        $resultPage->addBreadcrumb(__('Nwdthemes'), __('Nwdthemes'));
        $resultPage->addBreadcrumb(__('Slider Revolution'), __('Slider Revolution'));
        $resultPage->addBreadcrumb(__('Slider Overview'), __('Slider Overview'));
        return $resultPage;
    }

}
