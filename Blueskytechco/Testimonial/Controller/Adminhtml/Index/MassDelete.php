<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml\Index;

class MassDelete extends \Blueskytechco\Testimonial\Controller\Adminhtml\Action
{
    public function execute()
    {
        $testimonialIds = $this->getRequest()->getParam('testimonial');
        if (!is_array($testimonialIds) || empty($testimonialIds)) {
            $this->messageManager->addError(__('Please select testimonial(s).'));
        } else {
            $collection = $this->_testimonialCollectionFactory->create()
                ->addFieldToFilter('testimonial_id', ['in' => $testimonialIds]);
            try {
                foreach ($collection as $item) {
                    $item->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($testimonialIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
