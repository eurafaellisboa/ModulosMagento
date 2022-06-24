<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml\Index;

class NewAction extends \Blueskytechco\Testimonial\Controller\Adminhtml\Action
{
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
