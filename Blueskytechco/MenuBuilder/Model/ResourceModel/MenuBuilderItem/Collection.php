<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilderItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Blueskytechco\MenuBuilder\Model\MenuBuilderItem::class,
            \Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilderItem::class
        );
    }
}
