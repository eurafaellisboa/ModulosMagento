<?php
namespace Blueskytechco\Themeoption\Model\Config;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Default')],
            ['value' => '1170px', 'label' => __('1170px')],
            ['value' => '1400px', 'label' => __('1400px')],
            ['value' => '1920px', 'label' => __('1920px')],
            ['value' => '100%', 'label' => __('Full Width')],
            ['value' => 'box-layout', 'label' => __('Box Layout')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('Default'),
            '1170px' => __('1170px'),
            '1280px' => __('1400px'),
            '1920px' => __('1920px'),
            '100%' => __('Full Width'),
            'box-layout' => __('Box Layout')
        ];
    }
}
