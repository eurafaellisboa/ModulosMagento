<?php
namespace Blueskytechco\Themeoption\Model\Config;

class ThumbnailCropping implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1_1', 'label' => __('1:1')],
            ['value' => 'custom', 'label' => __('Custom')],
        ];
    }

    public function toArray()
    {
        return [
            '1_1' => __('1:1'),
            'custom' => __('Custom'),
        ];
    }
}