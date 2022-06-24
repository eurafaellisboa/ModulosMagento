<?php
/**
 * Copyright © 2021 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\DataObjectFactory;
use Blueskytechco\MenuBuilder\Model\MenuBuilderItemMetaFactory;

class MenuBuilderItem extends AbstractDb
{
    private $date;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    
    /**
     * @var MenuBuilderItemMetaFactory
     */
    protected $menuBuilderItemMetaFactory;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        MenuBuilderItemMetaFactory $menuBuilderItemMetaFactory,
        DateTime $date,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
        $this->date = $date;
        $this->menuBuilderItemMetaFactory = $menuBuilderItemMetaFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('menu_builder_item', 'entity_id');
    }
    
    /**
     * Get resource menu item meta
     *
     * @return resource
     */
    public function getResourceMenuItemMeta()
    {
        $resource = $this->menuBuilderItemMetaFactory->create()->getResource();
        return $resource;
    }


    /**
     * check category item by id.
     *
     */
    public function checkCategoryBuyId($item_object_id)
    {
        $table_category = $this->getTable('catalog_category_entity');
        $select = $this->getConnection()->select();
        $select->from($table_category);
        $select->where('entity_id = ?', $item_object_id);
        $category = $this->getConnection()->fetchRow($select);
        if ($category) {
            return true;
        }
        return false;
    }

    /**
     * check category item by parent id.
     *
     */
    public function checkCategoryBuyParentId($parent_id)
    {

        $table = $this->getTable('menu_builder_item');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('entity_id = ?', $parent_id);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['type_menu']) && $data['type_menu'] == 'category') {
            return $data['menu_object_id'];
        }
        return false;
    }
    
    /**
     * detete menu item.
     *
     */
    public function getDeteteItem($menu_id)
    {
        $resourceItemMeta = $this->getResourceMenuItemMeta();
        $table = $this->getTable('menu_builder_item');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('status = ?', 0);
        $select->where('menu_id = ?', $menu_id);
        $data = $this->getConnection()->fetchAll($select);
        foreach ($data as $k => $item) {
            $this->getConnection()->delete($table, ["entity_id = ".$item['entity_id'].""]);
            $resourceItemMeta->getDeteteItemMeta($item['entity_id']);
        }
    }

    /**
     * Check menu from ID.
     * @return array
     */
    public function getMenuBuilderItemById($id_menu)
    {
        $table = $this->getTable('menu_builder_item');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_id = ?', $id_menu);
        $select->where('status = ?', 1);
        $select->order('menu_order','ASC');
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    
    /**
     * detete menu items.
     *
     */
    public function getDeteteMenuItem($menu_id)
    {
        $resourceItemMeta = $this->getResourceMenuItemMeta();
        $table = $this->getTable('menu_builder_item');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_id = ?', $menu_id);
        $data = $this->getConnection()->fetchAll($select);
        foreach ($data as $k => $item) {
            $this->getConnection()->delete($table, ["entity_id = ".$item['entity_id'].""]);
            $resourceItemMeta->getDeteteItemMeta($item['entity_id']);
        }
    }
    
    /**
     * Get depth menu item
     *
     * @return float
     */
    public function getDepthItem($menu_item_id, $depth)
    {
        $table = $this->getTable('menu_builder_item');
        $table_meta = $this->getTable('menu_builder_item_meta');
        $select = $this->getConnection()->select();
        $select->from(
            ['a' => $table]
        )->join(
            ['b' => $table_meta],
            'b.menu_item_id=a.entity_id'
        );
        $select->where('a.entity_id = ?', $menu_item_id);
        $select->where('a.status = ?', 1);
        $select->where('b.meta_key = ?', 'parent_id');
        $data = $this->getConnection()->fetchRow($select);

        if (isset($data['meta_value'])) {
            $parent_id = $data['meta_value'];
            if ($this->checkCategoryBuyParentId($parent_id)) {
                $category_id = $this->checkCategoryBuyParentId($parent_id);
                if (!$this->checkCategoryBuyId($category_id)) {
                    return $depth;
                }
            }
            if (!$parent_id || $parent_id==0) {
                return $depth;
            } else {
                return $this->getDepthItem($parent_id, $depth+1);
            }
        }
        return $depth;
    }
    
    /**
     * update status menu item
     *
     */
    public function updateStatusItem($menu_id)
    {
        $table = $this->getTable('menu_builder_item');
        $this->getConnection()->update(
            $table,
            [
                'status' => 0,
            ],
            ["menu_id = ?" => $menu_id]
        );
    }
    
    /**
     * Get connection menu item
     *
     * @return array
     */
    public function getItems($menu_id)
    {
        $array_key = ['title','url','classes','lable','lable_color','icon_image',
            'submenu_type','full_width_multicolunm','full_width_block_content',
            'submenu_columns','dynamic_content_mul','dynamic_block_content',
            'submenu_bg_image','background_repeat','background_position','background_size',
            'block_content','block_top','block_bottom','block_left',
            'block_right','block_left_width','block_right_width','data_db_id','parent_id','font_icon'
        ];
        $meta_key = [];
        foreach ($array_key as $array) {
            $meta_key[] = "max(case when b.meta_key = '".$array."' then b.meta_value end) AS $array";
        }
        $table = $this->getTable('menu_builder_item');
        $table_meta = $this->getTable('menu_builder_item_meta');
        $select = $this->getConnection()->select();
        $select->from(
            ['a' => $table],
        )->joinLeft(
            ['b' => $table_meta],
            'b.menu_item_id=a.entity_id',
            [implode(",", $meta_key)]
        )->join(
            ['c' => $table_meta],
            'c.menu_item_id=a.entity_id',
            []
        );
        $select->where('a.menu_id = ?', $menu_id);
        $select->where('a.status = ?', 1);
        $select->where('c.meta_key = ?', 'parent_id');
        $select->where('c.meta_value = ?', '0');
        $select->group('a.entity_id');
        $select->order('a.menu_order', 'ASC');
        $query_string = str_replace("`", "", $select->__toString());
        $items = $this->getConnection()->fetchAll($query_string);
        return $items;
    }
    
    /**
     * Get connection menu item by parent
     *
     * @return array
     */
    public function getChildrenItems($menu_id, $id_item)
    {
        $array_key = ['title','url','classes','lable','lable_color','icon_image',
            'submenu_type','full_width_multicolunm','full_width_block_content',
            'submenu_columns','dynamic_content_mul','dynamic_block_content',
            'submenu_bg_image','background_repeat','background_position','background_size',
            'block_content','block_top','block_bottom','block_left',
            'block_right','block_left_width','block_right_width','data_db_id','parent_id','font_icon'
        ];
        $meta_key = [];
        foreach ($array_key as $array) {
            $meta_key[] = "max(case when b.meta_key = '".$array."' then b.meta_value end) AS $array";
        }
        $table = $this->getTable('menu_builder_item');
        $table_meta = $this->getTable('menu_builder_item_meta');
        $select = $this->getConnection()->select();
        $select->from(
            ['a' => $table],
        )->joinLeft(
            ['b' => $table_meta],
            'b.menu_item_id=a.entity_id',
            [implode(",", $meta_key)]
        )->join(
            ['c' => $table_meta],
            'c.menu_item_id=a.entity_id',
            []
        );
        $select->where('a.menu_id = ?', $menu_id);
        $select->where('a.status = ?', 1);
        $select->where('c.meta_key = ?', 'parent_id');
        $select->where('c.meta_value = ?', $id_item);
        $select->group('a.entity_id');
        $select->order('a.menu_order', 'ASC');
        $query_string = str_replace("`", "", $select->__toString());
        $items = $this->getConnection()->fetchAll($query_string);
        return $items;
    }
}
