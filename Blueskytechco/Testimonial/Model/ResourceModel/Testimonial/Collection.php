<?php

namespace Blueskytechco\Testimonial\Model\ResourceModel\Testimonial;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('Blueskytechco\Testimonial\Model\Testimonial', 'Blueskytechco\Testimonial\Model\ResourceModel\Testimonial');
    }

}
