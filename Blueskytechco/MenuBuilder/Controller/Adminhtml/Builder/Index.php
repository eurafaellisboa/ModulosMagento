<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Blueskytechco\MenuBuilder\Controller\Adminhtml\AbstractMenu;

class Index extends AbstractMenu
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Blueskytechco_MenuBuilder::menus_builder');
        $resultPage
            ->getConfig()
            ->getTitle()
            ->prepend(__('Menus'));

        return $resultPage;
    }
}
