<?php
namespace Blueskytechco\Themeoption\Model\Config;

class ProductPageLayout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Product Detail Default')],
            ['value' => 'product_style_1', 'label' => __('Product Detail Layout 1')],
            ['value' => 'product_style_2', 'label' => __('Product Detail Layout 2')],
            ['value' => 'product_style_3', 'label' => __('Product Detail Layout 3')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('Product Detail Default'),
            'product_style_1' => __('Product Detail Layout 1'),
            'product_style_2' => __('Product Detail Layout 2'),
            'product_style_3' => __('Product Detail Layout 3')
        ];
    }
}