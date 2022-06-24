<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml\Index;

class Index extends \Blueskytechco\Testimonial\Controller\Adminhtml\Action
{
   
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');

            return $resultForward;
        }

        $resultPage = $this->_resultPageFactory->create();

        return $resultPage;
    }
}
