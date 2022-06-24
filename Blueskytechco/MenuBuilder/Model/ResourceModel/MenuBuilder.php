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

class MenuBuilder extends AbstractDb
{
    private $date;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param Context $context
     * @param DateTime $date
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        DateTime $date,
        DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct($context);
        $this->date = $date;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('menu_builder', 'entity_id');
    }
    
    /**
     * Resource connection menu
     *
     * @return array
     */
    public function getMenus()
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    
    /**
     * Resource connection horizontal menu
     *
     * @return array
     */
    public function getHorizontalMenu()
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('type = ?', 1);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    
    /**
     * Resource connection vertical menu
     *
     * @return array
     */
    public function getVerticalMenu()
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('type = ?', 2);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
    
    /**
     * Check menu from type.
     * @return float
     */
    public function checkMenuBuilder($id_menu, $type)
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('entity_id = ?', $id_menu);
        $select->where('type = ?', $type);
        $data = $this->getConnection()->fetchRow($select);
        return $data?true:false;
    }

    /**
     * Get Identifier from menu.
     * @return float
     */
    public function getIdentifier($id_menu)
    {
        $data = $this->getMenuBuilderById($id_menu);
        $identifier = '';
        if (isset($data['identifier']) && $data['identifier']) {
            $identifier = $data['identifier'];
        }
        return $identifier;
    }

    /**
     * Check menu from ID.
     * @return array
     */
    public function getMenuBuilderById($id_menu)
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('entity_id = ?', $id_menu);
        $data = $this->getConnection()->fetchRow($select);
        return $data;
    }

    public function getMenuBuilderIdByIdentifier($id_menu)
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('identifier = ?', $id_menu);
        $data = $this->getConnection()->fetchRow($select);
        if(isset($data['entity_id'])){
            return $data['entity_id'];
        }
        return 0;
    }
    
    /**
     * Check menu from type.
     * @return array
     */
    public function checkMenuBuilderWidget($id_menu)
    {
        $table = $this->getTable('menu_builder');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('identifier = ?', $id_menu);
        $data = $this->getConnection()->fetchRow($select);
        return $data;
    }

    /**
     * Get all root category.
     * @return array
     */
    public function getCategories($parent_id, $level)
    {
        if (!$parent_id) {
            return false;
        }
        $table = $this->getTable('catalog_category_entity');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('parent_id = ?', $parent_id);
        $select->where('level = ?', $level);
        $select->join(
            ['a' => $this->getTable('catalog_category_entity_varchar')],
            $table.'.entity_id = a.entity_id',
            ['name'=>'a.value']
        )->where(
            'a.attribute_id=?',
            $this->getAttributeName()
        );
        if ($this->getIncludeInMenuId()) {
            $select->join(
                ['b' => $this->getTable('catalog_category_entity_int')],
                $table.'.entity_id = b.entity_id',
                ['include_in_menu'=>'b.value']
            )->where(
                'b.attribute_id=?',
                $this->getIncludeInMenuId()
            );
        }
        if ($this->getIsActive()) {
            $select->join(
                ['c' => $this->getTable('catalog_category_entity_int')],
                $table.'.entity_id = c.entity_id',
                ['is_active'=>'c.value']
            )->where(
                'c.attribute_id=?',
                $this->getIsActive()
            );
        }
        $categorys = $this->getConnection()->fetchAll($select);
        return $categorys;
    }

    /**
     * Get Root Catalog Id.
     * @return float
     */
    public function getRootCatalogId()
    {
        $table = $this->getTable('catalog_category_entity');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('position = ?', 0);
        $select->where('level = ?', 0);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['entity_id'])) {
            return $data['entity_id'];
        }
        return false;
    }

    /**
     * Get Category Attribute Include In Menu Id
     * @return float
     */
    public function getIncludeInMenuId()
    {
        $table = $this->getTable('eav_attribute');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('attribute_code = ?', 'include_in_menu');
        $select->where('entity_type_id = ?', 3);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['attribute_id'])) {
            return $data['attribute_id'];
        }
        return false;
    }

    /**
     * Get Category Attribute Is Active Id
     * @return float
     */
    public function getIsActive()
    {
        $table = $this->getTable('eav_attribute');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('attribute_code = ?', 'is_active');
        $select->where('entity_type_id = ?', 3);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['attribute_id'])) {
            return $data['attribute_id'];
        }
        return false;
    }

    /**
     * Get Category Attribute Name Id
     * @return float
     */
    public function getAttributeName()
    {
        $table = $this->getTable('eav_attribute');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('attribute_code = ?', 'name');
        $select->where('entity_type_id = ?', 3);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['attribute_id'])) {
            return $data['attribute_id'];
        }
        return false;
    }

    /**
     * Insert flat data menu
     * @return array
     */
    public function addContentFlatData($menu_id, $identifier, $store_id, $html)
    {
        $table = $this->getTable('menu_builder_flat_data');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_id = ?', $menu_id);
        $select->where('store_id = ?', $store_id);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['entity_id']) && $data['entity_id']) {
            $this->getConnection()->update(
                $table,
                [
                    'content_html' => $html,
                ],
                ['entity_id = ?' => $data['entity_id']]
            );
        } else {
            $this->getConnection()->insert(
                $table,
                [
                    'menu_id' => $menu_id,
                    'identifier' => $identifier,
                    'store_id' => $store_id,
                    'content_html' => $html,
                ]
            );
        }
        return true;
    }

    /**
     * Delete flat data menu
     * @return array
     */
    public function deleteFlatData($menu_id)
    {
        $table = $this->getTable('menu_builder_flat_data');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_id = ?', $menu_id);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['entity_id']) && $data['entity_id']) {
            $this->getConnection()->delete(
                $table,
                ['menu_id = ?' => $data['menu_id']]
            );
        }
    }

    /**
     * Get content flat data menu
     * @return array
     */
    public function getContentHtml($menu_id, $store_id)
    {
        $table = $this->getTable('menu_builder_flat_data');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_id = ?', $menu_id);
        $select->where('store_id = ?', $store_id);
        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['entity_id']) && isset($data['content_html'])) {
            $html = $data['content_html'];
            return $html;
        }
        return false;
    }
}
