<?php

namespace Blueskytechco\Testimonial\Controller\Adminhtml;

abstract class Index extends \Blueskytechco\Testimonial\Controller\Adminhtml\Testimonial
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Blueskytechco_Testimonial::testimonial_index');
    }
}
