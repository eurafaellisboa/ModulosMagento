<?php 
namespace Digitaria\SearchCustomerEmail\Controller\RecoverEmail;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Recover extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
   /**
 * Prepare global layout
 *
 * @return $this
 */ 
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Recover Email'));
        return $resultPage;
    }

}
