<?php

namespace MGS\Blog\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Magento\Framework\DB\Select;

class Category extends AbstractDb
{
    protected $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('mgs_blog_category', 'category_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        $this->checkUrlKeyExits($object);
        return parent::_beforeSave($object);
    }

    protected function _beforeDelete(AbstractModel $object)
    {
        $condition = ['category_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('mgs_blog_category_store'), $condition);
        $this->getConnection()->delete($this->getTable('mgs_blog_category_post'), $condition);
        return parent::_beforeDelete($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('mgs_blog_category_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = ['category_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['category_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    public function load(AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }
        return parent::load($object, $value, $field);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = [Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['mgs_blog_category_store' => $this->getTable('mgs_blog_category_store')],
                $this->getMainTable() . '.category_id = mgs_blog_category_store.category_id',
                []
            )->where(
                'status = ?',
                1
            )->where(
                'mgs_blog_category_store.store_id IN (?)',
                $storeIds
            )->order(
                'mgs_blog_category_store.store_id DESC'
            )->limit(
                1
            );
        }
        return $select;
    }

    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('mgs_blog_category_store')],
            'cp.category_id = cps.category_id',
            []
        )->where(
            'cp.url_key = ?',
            $identifier
        )->where(
            'cps.store_id IN (?)',
            $store
        );
        if (!is_null($isActive)) {
            $select->where('cp.status = ?', $isActive);
        }
        return $select;
    }

    protected function isNumericCategoryUrlKey(AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    protected function isValidCategoryUrlKey(AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    public function checkIdentifier($urlKey, $storeId)
    {
        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($urlKey, $stores, 1);
        $select->reset(Select::COLUMNS)->columns('cp.category_id')->order('cps.store_id DESC')->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    public function lookupStoreIds($categoryId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('mgs_blog_category_store'),
            'store_id'
        )->where(
            'category_id = ?',
            (int)$categoryId
        );
        return $connection->fetchCol($select);
    }

    public function checkUrlKeyExits(AbstractModel $object)
    {
        $stores = $object->getStores();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('mgs_blog_category'),
            'category_id'
        )
            ->where(
                'url_key = ?',
                $object->getUrlKey()
            )
            ->where(
                'category_id != ?',
                $object->getId()
            );
        $categoryIds = $connection->fetchCol($select);
        if (count($categoryIds) > 0 && is_array($stores)) {
            if (in_array('0', $stores)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL Key for specified store already exists.')
                );
            }
            $stores[] = '0';
            $select = $connection->select()->from(
                $this->getTable('mgs_blog_category_store'),
                'category_id'
            )
                ->where(
                    'category_id IN (?)',
                    $categoryIds
                )
                ->where(
                    'store_id IN (?)',
                    $stores
                );
            $result = $connection->fetchCol($select);
            if (count($result) > 0) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL Key for specified store already exists.')
                );
            }
        }
        return $this;
    }
}
