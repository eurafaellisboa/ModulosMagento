<?php
namespace Blueskytechco\SetProduct\Controller\Adminhtml\ProductSet;

use Blueskytechco\SetProduct\Controller\Adminhtml\ProductAction;

class Edit extends ProductAction
{ 
	public function execute()
    {
        $labels = $this->initLookbook();
        if (!$labels) {
			
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');
            return $resultRedirect;
        }

        $data = $this->_session->getData('blueskytechco_setproduct_data', true); 
        if (!empty($data)) {
			
            $labels->setData($data);
        }
		
        $this->coreRegistry->register('blueskytechco_setproduct', $labels);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Lookbook'));
        $title = $labels->getId() ? $labels->getName() : __('Lookbook');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}