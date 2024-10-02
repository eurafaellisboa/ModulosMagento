<?php

namespace Digitaria\RDMarketing\Controller\Adminhtml\CreateFields;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Digitaria\RDMarketing\Service\Connect;

class Result extends Action
{
    protected $context;
    protected $pageFactory;
    protected $connect;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Connect $connect
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->connect = $connect;
    }

    public function execute()
    {
                // Load the page and set data
                $page = $this->pageFactory->create();
                $block = $page->getLayout()->getBlock('content');
                if ($block) {
                    $block->setData('response_body', $responseBody);
                }
                return $page;
            
    }
}
