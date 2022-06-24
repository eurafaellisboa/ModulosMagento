<?php

namespace Blueskytechco\Themeoption\Model\Config;

class Fontsize implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('--- Select ---')], 
            ['value' => '8px', 'label' => __('8px')], 
            ['value' => '9px', 'label' => __('9px')], 
            ['value' => '10px', 'label' => __('10px')], 
            ['value' => '11px', 'label' => __('11px')], 
            ['value' => '12px', 'label' => __('12px')], 
            ['value' => '13px', 'label' => __('13px')], 
            ['value' => '14px', 'label' => __('14px')], 
            ['value' => '15px', 'label' => __('15px')], 
            ['value' => '16px', 'label' => __('16px')], 
            ['value' => '17px', 'label' => __('17px')], 
            ['value' => '18px', 'label' => __('18px')], 
            ['value' => '19px', 'label' => __('19px')], 
            ['value' => '20px', 'label' => __('20px')], 
            ['value' => '21px', 'label' => __('21px')], 
            ['value' => '22px', 'label' => __('22px')],
            ['value' => '23px', 'label' => __('23px')],
            ['value' => '24px', 'label' => __('24px')],
            ['value' => '25px', 'label' => __('25px')],
            ['value' => '26px', 'label' => __('26px')]
        ];
    }

    public function toArray()
    {
        return [ 
            '' => __('--- Select ---'), 
            '8px' => __('8px'), 
            '9px' => __('9px'), 
            '10px' => __('10px'), 
            '11px' => __('11px'), 
            '12px' => __('12px'), 
            '13px' => __('13px'), 
            '14px' => __('14px'), 
            '15px' => __('15px'), 
            '16px' => __('16px'), 
            '17px' => __('17px'), 
            '18px' => __('18px'), 
            '19px' => __('19px'), 
            '20px' => __('20px'),
            '21px' => __('21px'),
            '22px' => __('22px'),
            '23px' => __('23px'),
            '24px' => __('24px'),
            '25px' => __('25px'),
            '26px' => __('26px')
        ];
    }
}
