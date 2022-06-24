<?php
namespace Blueskytechco\Themeoption\Model\Config\Settings\Product;

class Imagehovereffects implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('Please select')], 
            ['value' => 'zoom', 'label' => __('Zoom')], 
            ['value' => 'zoom-overlay', 'label' => __('Zoom Overlay')], 
            ['value' => 'box-effect', 'label' => __('Box Effect')],
            ['value' => 'overlay', 'label' => __('Overlay')]
        ];
    }

    public function toArray()
    {
        return [
            'none' => __('Please select'), 
            'zoom' => __('Zoom'), 
            'zoom-overlay' => __('Zoom Overlay'), 
            'box-effect' => __('Box Effect'),
            'overlay' => __('Overlay')
        ];
    }
}
