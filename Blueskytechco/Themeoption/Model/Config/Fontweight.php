<?php

namespace Blueskytechco\Themeoption\Model\Config;

class Fontweight implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('--- Select ---')], 
            ['value' => '300', 'label' => __('300')], 
            ['value' => '400', 'label' => __('400')], 
            ['value' => '500', 'label' => __('500')], 
            ['value' => '600', 'label' => __('600')], 
            ['value' => '700', 'label' => __('700')], 
            ['value' => '800', 'label' => __('800')], 
            ['value' => '900', 'label' => __('900')]
        ];
    }

    public function toArray()
    {
        return [ 
            '' => __('--- Select ---'), 
            '300' => __('300'), 
            '400' => __('400'), 
            '500' => __('500'), 
            '600' => __('600'), 
            '700' => __('700'), 
            '800' => __('800'), 
            '900' => __('900')
        ];
    }
}
