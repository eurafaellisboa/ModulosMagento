<?php
namespace Blueskytechco\Themeoption\Model\Config;

class Footertype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'footer_layout_1', 'label' => __('Layout 1 - Default')],
            ['value' => 'footer_layout_2', 'label' => __('Layout 2')],
            ['value' => 'footer_layout_3', 'label' => __('Layout 3')],
            ['value' => 'footer_layout_4', 'label' => __('Layout 4')],
            ['value' => 'footer_layout_5', 'label' => __('Layout 5')],
            ['value' => 'footer_layout_6', 'label' => __('Layout 6')],
        ];
    }

    public function toArray()
    {
        return [
            'footer_layout_1' => __('Layout 1 - Default'),
            'footer_layout_2' => __('Layout 2'),
            'footer_layout_3' => __('Layout 3'),
            'footer_layout_4' => __('Layout 4'),
            'footer_layout_5' => __('Layout 5'),
            'footer_layout_6' => __('Layout 6'),
        ];
    }
}