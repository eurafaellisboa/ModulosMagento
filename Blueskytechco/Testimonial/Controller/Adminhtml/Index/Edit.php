<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml\Index;

class Edit extends \Blueskytechco\Testimonial\Controller\Adminhtml\Action
{
    
    public function execute()
    {
        $id = $this->getRequest()->getParam('testimonial_id');
        $storeViewId = $this->getRequest()->getParam('store');
        $model = $this->_testimonialFactory->create();

        if ($id) {
            $model->setStoreViewId($storeViewId)->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Testimonial no longer exists.'));
                $resultRedirect = $this->_resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('testimonial', $model);

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
