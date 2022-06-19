<?php

namespace Abzertech\Smtp\Model;

class Core extends \Magento\Framework\Model\AbstractModel
{
    /*
     * Initialize
     */

    public function _construct()
    {
        $this->_init(\Abzertech\Smtp\Model\ResourceModel\Core::class);
    }
}
