<?php

namespace Digitaria\RDMarketing\Controller\Adminhtml\CreateFields;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $context;
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
                /*
                $page = $this->pageFactory->create();
                $block = $page->getLayout()->getBlock('content');
                if ($block) {
                    $block->setData('response_body', $responseBody);
                }
                return $page;
                */

    $this->_view->loadLayout();
    $this->_view->getPage()->getConfig()->getTitle()->prepend(__('RD Station Create Fields'));
    $this->_view->renderLayout();
    }
}