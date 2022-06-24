<?php

namespace Blueskytechco\Testimonial\Model\ResourceModel;

class Testimonial extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('blueskytechco_testimonial', 'testimonial_id');
    }
}
