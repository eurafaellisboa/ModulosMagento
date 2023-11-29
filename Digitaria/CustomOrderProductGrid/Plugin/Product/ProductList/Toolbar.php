<?php
namespace Digitaria\CustomOrderProductGrid\Plugin\Product\ProductList;

class Toolbar
{
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $collection
    ) {
        $currentOrder = $subject->getCurrentOrder();
        if ($currentOrder == "cor" ) {
            $dir = $subject->getCurrentDirection();
            $collection->getSelect()->order('cor '.$dir); // you can add filter as per your requirement.
        }
        return $proceed($collection);
    }
}