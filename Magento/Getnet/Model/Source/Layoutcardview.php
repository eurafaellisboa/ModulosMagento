<?php
namespace Magento\Getnet\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class Layoutcardview implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Layout default')
            ],
            [
                'value' =>  2,
                'label' =>__('Layout custom')
            ]
        ];
    }
}
