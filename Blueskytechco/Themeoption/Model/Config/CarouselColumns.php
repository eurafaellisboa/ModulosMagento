<?php
namespace Blueskytechco\Themeoption\Model\Config;

class CarouselColumns implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '2', 'label' => __('2 Columns')],
            ['value' => '3', 'label' => __('3 Columns')],
            ['value' => '4', 'label' => __('4 Columns')],
            ['value' => '5', 'label' => __('5 Columns')],
            ['value' => '6', 'label' => __('6 Columns')],
            ['value' => '7', 'label' => __('7 Columns')],
            ['value' => '8', 'label' => __('8 Columns')],
            ['value' => '9', 'label' => __('9 Columns')],
            ['value' => '10', 'label' => __('10 Columns')],
            ['value' => '11', 'label' => __('11 Columns')],
            ['value' => '12', 'label' => __('12 Columns')]
        ];
    }

    public function toArray()
    {
        return [
            '2' => __('2 Columns'),
            '3' => __('3 Columns'),
            '4' => __('4 Columns'),
            '5' => __('5 Columns'),
            '6' => __('6 Columns'),
            '7' => __('7 Columns'),
            '8' => __('8 Columns'),
            '9' => __('9 Columns'),
            '10' => __('10 Columns'),
            '11' => __('11 Columns'),
            '12' => __('12 Columns')
        ];
    }
}