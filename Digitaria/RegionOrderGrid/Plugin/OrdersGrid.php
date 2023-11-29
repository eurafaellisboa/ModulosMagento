<?php
namespace Digitaria\RegionOrderGrid\Plugin;

class OrdersGrid
{
    public function afterGetReport($subject, $collection, $requestName)
    {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $collection;
        }

        if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) {
            $orderAddressTable = $collection->getResource()->getTable('sales_order_address');
            $collection->getSelect()->joinLeft(
                ['oat' => $orderAddressTable],
                'oat.parent_id = main_table.entity_id AND oat.address_type = \'shipping\'',
                ['region']
            );
        }

        return $collection;
    }
}
