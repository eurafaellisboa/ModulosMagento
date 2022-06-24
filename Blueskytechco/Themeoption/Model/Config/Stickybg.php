<?php

namespace Blueskytechco\Themeoption\Model\Config;

class Stickybg implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Default')], 
            ['value' => 'custom', 'label' => __('Custom')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('Default'), 
            'custom' => __('Custom')
        ];
    }
}
