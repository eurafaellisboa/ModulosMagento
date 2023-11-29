<?php
namespace Digitaria\CustomOrderProductGrid\Plugin;

class Config
{
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $options[] = ["cores" => __("Cor Edit")];
        return $options;
    }
}