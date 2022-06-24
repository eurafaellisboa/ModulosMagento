<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Blueskytechco\MenuBuilder\Controller\Adminhtml\AbstractMenu;
use Magento\Framework\Controller\ResultFactory;

class Edit extends AbstractMenu
{
    /**
     *
     * @return mixed
     */
    public function execute()
    {
        $patternId = $this->getRequest()->getParam('id');
        $pattern = $this->menuBuilderFactory->create();
        if ($patternId) {
            try {
                $pattern = $this->menuBuilderFactory->create()->load($patternId);
                $pageTitle = sprintf("%s", $pattern->getName());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This pattern no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                return $resultRedirect->setPath('menus/builder');
            }
        } else {
            $pageTitle = __('New Menus');
        }
        $this->registry->register('menus_builder', $pattern);
        $breadcrumb = $patternId ? __('Edit Menus') : __('New Menus');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Blueskytechco_MenuBuilder::menus_builder');
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('Menus'));
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);
        return $resultPage;
    }
}
