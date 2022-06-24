<?php
namespace Blueskytechco\Themeoption\Model\Config;

class ListImageType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'product_base_image', 'label' => __('Product Base Image')],
            ['value' => 'product_small_image', 'label' => __('Product Small Image')],
            ['value' => 'product_thumbnail_image', 'label' => __('Product Thumbnail Image')]
        ];
    }
}
