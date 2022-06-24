<?php
namespace Blueskytechco\Themeoption\Model\Config;

class Headertype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'header_layout_1', 'label' => __('Layout 1 - Default')],
            ['value' => 'header_layout_2', 'label' => __('Layout 2')],
            ['value' => 'header_layout_3', 'label' => __('Layout 3')],
            ['value' => 'header_layout_4', 'label' => __('Layout 4')],
            ['value' => 'header_layout_5', 'label' => __('Layout 5')],
            ['value' => 'header_layout_6', 'label' => __('Layout 6 ')],
            ['value' => 'header_layout_7', 'label' => __('Layout 7 ')],
            ['value' => 'header_layout_8', 'label' => __('Layout 8 ')],
            ['value' => 'header_layout_9', 'label' => __('Layout 9 ')],
        ];
    }

    public function toArray()
    {
        return [
            'header_layout_1' => __('Layout 1 - Default'),
            'header_layout_2' => __('Layout 2'),
            'header_layout_3' => __('Layout 3'),
            'header_layout_4' => __('Layout 4'),
            'header_layout_5' => __('Layout 5'),
            'header_layout_6' => __('Layout 6'),
            'header_layout_7' => __('Layout 7'),
            'header_layout_8' => __('Layout 8'),
            'header_layout_9' => __('Layout 9'),
        ];
    }
}