<?php
namespace Blueskytechco\Themeoption\Model\Config;

class DisplayTabs implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Tabs Default')],
            ['value' => 'accordions', 'label' => __('Tabs Accordions')],
            ['value' => 'accordions-2', 'label' => __('Tabs Accordions 2')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('Tabs Default'),
            'accordions' => __('Tabs Accordions'),
            'accordions-2' => __('Tabs Accordions 2')
        ];
    }
}