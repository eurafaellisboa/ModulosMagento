<?php

namespace Magento\Getnet\Model\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const SANDBOX = 'sandbox';
    const PRODUCTION = 'production';

    public function toOptionArray()
    {
        return [
            [
                'value' => "PRODUCTION",
                'label' => __('Production')
            ],
            [
                'value' =>  "SANDBOX",
                'label' =>__('Sandbox')
            ],
            [
                'value' =>  "HOMOLOG",
                'label' =>__('Homolog')
            ]
        ];
    }
}
