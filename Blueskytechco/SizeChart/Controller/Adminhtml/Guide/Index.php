<?php

namespace Blueskytechco\SizeChart\Controller\Adminhtml\Guide;

use Blueskytechco\SizeChart\Controller\Adminhtml\AbstractStore;

class Index extends AbstractStore
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Blueskytechco_SizeChart::sizechart'); 
        $resultPage
            ->getConfig()
            ->getTitle() 
            ->prepend(__('Size Guide'));

        return $resultPage;
    }
}
