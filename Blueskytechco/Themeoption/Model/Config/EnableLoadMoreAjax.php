<?php
namespace Blueskytechco\Themeoption\Model\Config;

class EnableLoadMoreAjax implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'button', 'label' => __('Load More Button')],
            ['value' => 'scroll', 'label' => __('Infinit scrolling')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('None'),
            'button' => __('Load More Button'),
            'scroll' => __('Infinit scrolling')
        ];
    }
}