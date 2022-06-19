<?php

namespace Abzertech\Smtp\Block\Adminhtml;

class Email extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Core constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_email_log';
        $this->_blockGroup = 'Abzertech_Smtp';
        $this->_headerText = __('SMTP Email Log');
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
