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
namespace FME\ShareCart\Model\ResourceModel;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $storeManager;
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['ft_store' => $this->getTable($tableName)])
                ->where('ft_store.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchPairs($select);
           // echo $select->__toString();
            if ($result) {
                foreach ($this as $item) {
                    $entityId = $item->getData($columnName);
                    if (!isset($result[$entityId])) {
                        continue;
                    }
                    if ($result[$entityId] == 0) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData($columnName)];
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$entityId]]);
                    //$item->setData('dislike',10);
                    //$item->setData('like',1);
                  //  print_r($item->getData());
                }
            }
           // print_r($this->getData());
        }
    }
    public function performAfterForLikes($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            
            //echo $select->__toString();
            //exit;
   
                foreach ($this as $item) {
                    
                    $id=$item->getData("testimonial_id");
                    $select = $connection->select()
                        ->from(['ft_vote' => $this->getTable($tableName)])
                        ->where('ft_vote.' . $columnName . ' =?', $id)
                        ->where('ft_vote.' .'islike'. '='.'1');
                    $result = $connection->fetchPairs($select);
                    if(count($result))
                    {
                        $item->setData('like',count($result));                            
                    }else{
                        $item->setData('like',0);
                    }

                }
        
        }


    }
    public function performAfterForDisLikes($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            
            //echo $select->__toString();
            //exit;
   
                foreach ($this as $item) {
                    
                    $id=$item->getData("testimonial_id");
                    $select = $connection->select()
                        ->from(['ft_vote' => $this->getTable($tableName)])
                        ->where('ft_vote.' . $columnName . ' =?', $id)
                        ->where('ft_vote.' .'islike'. '='.'0');
                    $result = $connection->fetchPairs($select);
                    if(count($result))
                    {
                        $item->setData('dislike',count($result));                            
                    }else{
                        $item->setData('dislike',0);
                    }

                }
        
        }


    }
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    abstract public function addStoreFilter($store, $withAdmin = true);
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
    }

    protected function joinStoreRelationTable($tableName, $columnName)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = store_table.' . $columnName,
                []
            )->group(
                'main_table.' . $columnName
            );
        }
        parent::_renderFiltersBefore();
    }

    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        
        return $countSelect;
    }
}
