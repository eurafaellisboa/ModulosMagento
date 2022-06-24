<?php
namespace Smartwave\Porto\Model\Config\Design\Color;

class Bgcolor implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'primary', 'label' => __('Primary')],
            ['value' => 'light', 'label' => __('Light')],
            ['value' => 'dark', 'label' => __('Dark')]
        ];
    }

    public function toArray()
    {
        return [
            'primary' => __('Primary'),
            'light' => __('Light'),
            'dark' => __('Dark')
        ];
    }
}
