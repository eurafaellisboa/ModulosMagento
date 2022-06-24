<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml\Index;

class Delete extends \Blueskytechco\Testimonial\Controller\Adminhtml\Action
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        try {
            $item = $this->_testimonialFactory->create()->setId($id);
            $item->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
