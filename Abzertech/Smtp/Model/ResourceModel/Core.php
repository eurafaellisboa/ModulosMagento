<?php

namespace Abzertech\Smtp\Model\ResourceModel;

class Core extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /*
     * Initialize
     */

    protected function _construct()
    {
        $this->_init('abzertech_smtp_log', 'id');
    }
}
