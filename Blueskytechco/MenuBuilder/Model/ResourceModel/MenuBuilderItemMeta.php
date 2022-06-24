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

class MenuBuilderItemMeta extends AbstractDb
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
        $this->_init('menu_builder_item_meta', 'entity_id');
    }
    
    public function getDataMeta($id_item, $key_item)
    {
        $select = $this->getConnection()->select();
        $select->from($this->getTable('menu_builder_item_meta'));
        $select->where('menu_item_id = ?', $id_item);
        $select->where('meta_key = ?', $key_item);
        $data = $this->getConnection()->fetchRow($select);
        return $data;
    }
    
    public function getDeteteItemMeta($id_item)
    {
        $table = $this->getTable('menu_builder_item_meta');
        $readCollection = $this->getConnection();
        $readCollection->delete($table, ["menu_item_id = ".$id_item.""]);
    }
    
    public function getDataItemMeta($id_item)
    {
        $table = $this->getTable('menu_builder_item_meta');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_item_id = ?', $id_item);
        $data = $this->getConnection()->fetchAll($select);
        $data_meta = [];
        foreach ($data as $k => $item) {
            $data_meta[$item['meta_key']] = $item['meta_value'];
        }
        return $data_meta;
    }
    
    public function getDepthItem($menu_item_id)
    {
        $resourceItemMeta = $this->menuBuilderItemMetaFactory->create()->getResource();
        $table = $this->getTable('menu_builder_item_meta');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('entity_id = ?', $menu_item_id);
        $data = $this->getConnection()->fetchRow($select);
        foreach ($data as $k => $item) {
            $this->getConnection()->delete($table, ["entity_id = ".$item['entity_id'].""]);
            $resourceItemMeta->getDeteteItemMeta($item['entity_id']);
        }
    }

    public function getMenuBuilderItemMetaById($id_item)
    {
        $table = $this->getTable('menu_builder_item_meta');
        $select = $this->getConnection()->select();
        $select->from($table);
        $select->where('menu_item_id = ?', $id_item);
        $data = $this->getConnection()->fetchAll($select);
        return $data;
    }
}
