<?php
namespace Digitaria\CouponOrderGrid\Model\ResourceModel\Order\Grid;
 
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as CoreFetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as CoreEntityFactory;
use Magento\Framework\Event\ManagerInterface as CoreEventManager;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as CoreSalesGrid;
use Psr\Log\LoggerInterface as Logger;
 
class Collection extends CoreSalesGrid
{
    /**
     * @param CoreEntityFactory $entityFactory
     * @param Logger            $logger       
     * @param CoreFetchStrategy $fetchStrategy
     * @param CoreEventManager  $eventManager 
     * @param string            $mainTable    
     * @param string            $resourceModel
     */
    public function __construct(
        CoreEntityFactory $entityFactory,
        Logger $logger,
        CoreFetchStrategy $fetchStrategy,
        CoreEventManager $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
 
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('sales_order');
        $this->getSelect()->joinLeft(
            $joinTable,
            'main_table.entity_id = sales_order.entity_id',
            ['coupon_code','coupon_rule_name']
        );
        parent::_renderFiltersBefore();
    }
}