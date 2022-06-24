<?php
namespace Blueskytechco\ProductWidgetAdvanced\Controller\Adminhtml\Collect;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class BestSeller extends Action
{
    
    protected $resultPageFactory;
    protected $_cronJobObject;

    public function __construct(
        Context $context,
        \Blueskytechco\ProductWidgetAdvanced\Cron\Collect\BestSeller $bestSeller
    ) {
        $this->_cronJobObject = $bestSeller;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_cronJobObject->execute();
        $this->messageManager->addSuccess(__('Collect Product Data BestSeller Successfully.'));
        $this->_redirect('adminhtml/system_config/edit/section/collect_product_data');
    }
}
