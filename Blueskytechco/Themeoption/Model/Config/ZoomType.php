<?php
namespace Blueskytechco\Themeoption\Model\Config;

class ZoomType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => '1', 'label' => __('Inner Zoom #1')],
            ['value' => '1', 'label' => __('External Zoom')],
            ['value' => '3', 'label' => __('Inner Zoom #2')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('None'),
            '1' => __('Inner Zoom #1'),
            '1' => __('External Zoom'),
            '3' => __('Inner Zoom #2')
        ];
    }
}