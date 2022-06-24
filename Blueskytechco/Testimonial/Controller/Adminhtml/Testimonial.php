<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml;

abstract class Testimonial extends \Magento\Backend\App\Action
{
    protected $_jsHelper;
    protected $_storeManager;
    protected $_resultForwardFactory;
    protected $_resultLayoutFactory;
    protected $_resultPageFactory;
    protected $_resultRedirectFactory;
    protected $_testimonialFactory;
    protected $_testimonialCollectionFactory;
    protected $_coreRegistry;
    protected $_fileFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Blueskytechco\Testimonial\Model\TestimonialFactory $testimonialFactory,
        \Blueskytechco\Testimonial\Model\ResourceModel\Testimonial\CollectionFactory $testimonialCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_jsHelper = $jsHelper;

        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_resultForwardFactory = $resultForwardFactory;

        $this->_testimonialFactory = $testimonialFactory;
        $this->_testimonialCollectionFactory = $testimonialCollectionFactory;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
