<?php
namespace Smartwave\Porto\Model\Config\Design\Minicart;

class Icon implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Design 1')],
            ['value' => '2', 'label' => __('Design 2')]
        ];
    }

    public function toArray()
    {
        return [
            '1' => __('Design 1'),
            '2' => __('Design 2')
        ];
    }
}
