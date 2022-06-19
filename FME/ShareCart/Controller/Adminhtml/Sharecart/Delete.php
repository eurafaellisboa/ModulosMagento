<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Controller\Adminhtml\Sharecart;

class Delete extends \Magento\Backend\App\Action
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_ShareCart::savedcart');
    }

    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('sharecart_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('FME\ShareCart\Model\Sharecart');
                $model->load($id);
              //  $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('Cart has been deleted.'));
                // go to grid
                // $this->_eventManager->dispatch(
                //     'adminhtml_testimonialstestimonials_on_delete',
                //     ['title' => $title, 'status' => 'success']
                // );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // $this->_eventManager->dispatch(
                //     'adminhtml_testimonialstestimonials_on_delete',
                //     ['title' => $title, 'status' => 'fail']
                // );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['sharecart_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Cart to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
