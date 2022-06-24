<?php

namespace Blueskytechco\SetProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Blueskytechco\SetProduct\Model\ProductSetFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Result\PageFactory;

abstract class ProductAction extends Action
{
    public $lookbookFactory;
    public $coreRegistry;
	public $resultPageFactory;
    protected $_logger;
    public function __construct(
        ProductSetFactory $lookbookFactory,
		PageFactory $resultPageFactory,
		DateTime $date,
        Registry $coreRegistry,
        Context $context
    ) {
        $this->lookbookFactory  = $lookbookFactory;
        $this->coreRegistry = $coreRegistry;
		$this->date = $date;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    protected function initLookbook($register = false)
    {
        $lookbookId = (int)$this->getRequest()->getParam('id');
		
        $lookbook = $this->lookbookFactory->create();
        if ($lookbookId) {
            $lookbook->load($lookbookId);
            if (!$lookbook->getId()) {
                $this->messageManager->addErrorMessage(__('This Lookbook no longer exists.'));
                return false;
            }
        }
        if ($register) {
            $this->coreRegistry->register('blueskytechco_setproduct', $lookbook); 
        }
        return $lookbook;
    }
}
