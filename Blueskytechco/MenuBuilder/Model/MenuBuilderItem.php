<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model;

use Magento\Framework\Model\AbstractModel;
use Blueskytechco\MenuBuilder\Model\ResourceModel\MenuBuilderItem as MenuBuilderItemResourceModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

class MenuBuilderItem extends AbstractModel
{
    
    /**
     * Pattern constructor.
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct(
            $context,
            $registry
        );
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init(MenuBuilderItemResourceModel::class);
    }
    
    /**
     * Create Menu Builder
     * @return \Blueskytechco\MenuBuilder\Model\MenuBuilderItem
     */
    public function createMenuBuilderItem($menu_id, $item_object_id, $menu_item_name, $type_menu)
    {
        $this->setData([
            'menu_id' => $menu_id,
            'item_title' => $menu_item_name,
            'menu_object_id' => $item_object_id,
            'type_menu' => $type_menu,
            'menu_order' => 0,
        ])->setId(null)->save();
        return $this;
    }
}
