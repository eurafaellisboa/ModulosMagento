<?php
namespace Blueskytechco\Themeoption\Model\Config;

class Fontfamily implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'custom', 'label' => __('Custom Fonts')], 
            ['value' => 'google', 'label' => __('Google Fonts')]
        ];
    }

    public function toArray()
    {
        return [
            'custom' => __('Custom Fonts'), 
            'google' => __('Google Fonts')
        ];
    }
}
