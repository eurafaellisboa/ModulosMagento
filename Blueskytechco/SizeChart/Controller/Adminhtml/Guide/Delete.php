<?php

namespace Blueskytechco\SizeChart\Controller\Adminhtml\Guide;

use Blueskytechco\SizeChart\Controller\Adminhtml\AbstractStore;

/**
 * Class Save
 *
 * @package Blueskytechco\SizeChart\Controller\Adminhtml\Guide
 */
class Delete extends AbstractStore
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $store = $this->sizeChartFactory->create()->load($id);
            if ($store->getEntityId()) {
                $store->delete();
                $this->messageManager->addSuccessMessage(__('Success'));
                return $resultRedirect->setPath('sizechart/guide');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath(
                'sizechart/guide/edit',
                ['id' => $id]
            );
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an store to delete.'));
        return $resultRedirect->setPath('sizechart/guide');
    }
}
