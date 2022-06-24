<?php

namespace Blueskytechco\SetProduct\Controller\Adminhtml\ProductSet;

use Exception;
use Magento\Framework\Controller\Result\Redirect;
use Blueskytechco\SetProduct\Controller\Adminhtml\ProductAction;

class Delete extends ProductAction
{

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->lookbookFactory->create()->load($id)->delete();

                $this->messageManager->addSuccessMessage(__('Deleted Success.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Delete was not found.'));
        }
        $resultRedirect->setPath('addproductsset/*/');
        return $resultRedirect;
    }
}
