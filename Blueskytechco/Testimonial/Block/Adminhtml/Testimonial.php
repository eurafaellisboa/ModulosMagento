<?php

namespace Blueskytechco\Testimonial\Block\Adminhtml;

class Testimonial extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_testimonial';
        $this->_blockGroup = 'Blueskytechco_Testimonial';
        $this->_headerText = __('Testimonial');
        $this->_addButtonLabel = __('Add New Testimonial');
        parent::_construct();
    }
}
