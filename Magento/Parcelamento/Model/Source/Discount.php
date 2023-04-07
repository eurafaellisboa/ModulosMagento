<?php

namespace Magento\Parcelamento\Model\Source;

class Discount implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('fixed')
            ],
            [
                'value' =>  2,
                'label' =>__('Percents')
            ]
        ];
    }
}
