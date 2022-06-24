<?php
namespace Blueskytechco\OnePageCheckout\Controller\Adminhtml\Field;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Position
 * @package Blueskytechco\OnePageCheckout\Controller\Adminhtml\Field
 */
class Manage extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Fields'));
        $resultPage->addBreadcrumb(__('One Page Checkout'), __('One Page Checkout'));
        $resultPage->addBreadcrumb(__('Manage Fields'), __('Manage Fields'));

        return $resultPage;
    }
}
