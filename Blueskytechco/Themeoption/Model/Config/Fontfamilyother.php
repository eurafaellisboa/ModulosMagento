<?php
namespace Blueskytechco\Themeoption\Model\Config;

class Fontfamilyother implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default')], 
            ['value' => 'custom', 'label' => __('Custom Fonts')], 
            ['value' => 'google', 'label' => __('Google Fonts')]
        ];
    }

    public function toArray()
    {
        return [
            'default' => __('Default'), 
            'custom' => __('Custom Fonts'), 
            'google' => __('Google Fonts')
        ];
    }
}
