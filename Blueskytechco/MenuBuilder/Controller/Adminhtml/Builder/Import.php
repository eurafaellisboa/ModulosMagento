<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Controller\Adminhtml\Builder;

use Blueskytechco\MenuBuilder\Controller\Adminhtml\AbstractMenu;
use Magento\Framework\Controller\ResultFactory;

class Import extends AbstractMenu
{
    /**
     *
     * @return mixed
     */
    public function execute()
    {
        $breadcrumb = __('Import Menus');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb($breadcrumb, $breadcrumb);
        $resultPage->getConfig()->getTitle()->prepend(__('Import Menus'));
        return $resultPage;
    }
}
