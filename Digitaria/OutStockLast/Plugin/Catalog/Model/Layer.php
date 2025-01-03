<?php
namespace Digitaria\OutStockLast\Plugin\Catalog\Model;

/**
 * Class Layer
 * @package Digitaria\OutStockLast\Plugin\Catalog\Model\Layer
 */
class Layer
{
  /**
  * Sort items that are not salable last
  *
  * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
  */
  public function afterGetProductCollection(
      \Magento\Catalog\Model\Layer $subject,
      \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
  ) {
      $collection->getSelect()->order('is_salable DESC');
      return $collection;
  }
}
