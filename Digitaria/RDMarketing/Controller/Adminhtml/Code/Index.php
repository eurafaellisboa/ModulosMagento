<?php
namespace Digitaria\RDMarketing\Controller\Adminhtml\Code;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

        /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Context $context, PageFactory $pageFactory, ScopeConfigInterface $scopeConfig)
    {
        $this->pageFactory = $pageFactory;
        $this->scopeConfig = $scopeConfig;
        
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
{

    $this->_view->loadLayout();
    $this->_view->getPage()->getConfig()->getTitle()->prepend(__('RD Station Code'));
    $this->_view->renderLayout();
}

}
