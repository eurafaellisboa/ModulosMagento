<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Blueskytechco\MenuBuilder\Controller\Adminhtml\AbstractMenu;

class Delete extends AbstractMenu
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $resourceMenus = $this->menuBuilderFactory->create()->getResource();
        try {
            $menus = $this->menuBuilderFactory->create()->load($id);
            $resourceItem = $this->menuBuilderItemFactory->create()->getResource();
            if ($menus->getEntityId()) {
                $menus->delete();
                $resourceMenus->deleteFlatData($menus->getEntityId());
                $resourceItem->getDeteteMenuItem($menus->getEntityId());
                $this->messageManager->addSuccessMessage(__('Delete Success'));
                return $resultRedirect->setPath('menus/builder');
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath(
                'menus/builder/edit',
                ['id' => $id]
            );
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an store to delete.'));
        return $resultRedirect->setPath('menus/builder');
    }
}
