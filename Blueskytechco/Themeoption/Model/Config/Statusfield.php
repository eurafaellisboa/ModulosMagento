<?php
namespace Blueskytechco\Themeoption\Model\Config;

class Statusfield implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'enable', 'label' => __('Enable')], 
            ['value' => 'disable', 'label' => __('Disable')]
        ];
    }

    public function toArray()
    {
        return [
            'enable' => __('Enable'), 
            'disable' => __('Disable')
        ];
    }
}
