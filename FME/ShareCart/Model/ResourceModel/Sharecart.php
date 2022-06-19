<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\Sharecart\Model\ResourceModel;

class Sharecart extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
        
    protected $_storeManager;
        
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }
           
    protected function _construct()
    {
        $this->_init('fme_sharecart', 'sharecart_id'); //hello is table of module
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
      
        return parent::_afterLoad($object);
    }
    protected function _afterSave(
        \Magento\Framework\Model\AbstractModel $object
    ) {
        
        return parent::_afterSave($object);
    }
    protected function isNumericIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }
    protected function isValidIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }
    public function lookupStoreIds($testimonialid)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('fme_testimonials_store'),
            'store_id'
        )->where(
            'testimonial_id = ?',
            (int)$testimonialid
        );

        return $connection->fetchCol($select);
    }
    public function getIsUniqueTestimonialToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->getConnection()->select()->from(
            ['ft' => $this->getMainTable()]
        )->join(
            ['fts' => $this->getTable('fme_testimonials_store')],
            'ft.testimonial_id = fts.testimonial_id',
            []
        )->where(
            'ft.identifier = ?',
            $object->getData('identifier')
        )->where(
            'fts.store_id IN (?)',
            $stores
        );

        if ($object->getId()) { //in edit mode, compare other then current
            $select->where('ft.testimonial_id <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        )->columns(
            'ft.testimonial_id'
        )->order(
            'fts.store_id DESC'
        )->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
    // protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    // {
    //     $select = $this->getConnection()->select()->from(
    //         ['ft' => $this->getMainTable()]
    //     )->join(
    //         ['fts' => $this->getTable('fme_testimonials_store')],
    //         'ft.testimonial_id = fts.testimonial_id',
    //         []
    //     )->where(
    //         'ft.identifier = ?',
    //         $identifier
    //     )->where(
    //         'fts.store_id IN (?)',
    //         $store
    //     );

    //     if (!is_null($isActive)) {
    //         $select->where('ft.status = ?', $isActive);
    //     }

    //     return $select;
    // }
}
