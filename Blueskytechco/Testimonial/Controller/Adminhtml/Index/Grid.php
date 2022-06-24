<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml\Index;

class Grid extends \Blueskytechco\Testimonial\Controller\Adminhtml\Action
{
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();

        return $resultLayout;
    }
}
