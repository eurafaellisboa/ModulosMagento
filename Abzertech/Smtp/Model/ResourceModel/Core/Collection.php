<?php

namespace Abzertech\Smtp\Model\ResourceModel\Core;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /*
     * Initialize
     */

    public function _construct()
    {
        $this->_init(
            \Abzertech\Smtp\Model\Core::class,
            \Abzertech\Smtp\Model\ResourceModel\Core::class
        );
    }
}
