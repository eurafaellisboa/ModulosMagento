<?php
namespace Smartwave\Porto\Model\Config\Settings\General;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1140', 'label' => __('1140px (Default)')],
            ['value' => '1220', 'label' => __('1220px')],
            ['value' => 'full_width', 'label' => __('Full Width')]
        ];
    }

    public function toArray()
    {
        return [
            '1140' => __('1140px (Default)'),
            '1220' => __('1220px'),
            'full_width' => __('Full Width')
        ];
    }
}
