<?php
namespace Blueskytechco\Themeoption\Model\Config;

class PageLayout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Default Layout')],
            ['value' => 'grid', 'label' => __('Grid Layout')],
            ['value' => 'masonry', 'label' => __('Masonry Layout')],
            ['value' => 'fullwidth', 'label' => __('Full Width Layout')],
            ['value' => 'packery', 'label' => __('Packery Layout')],
            ['value' => 'sidebar-canvas', 'label' => __('Filter Canvas')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('Default Layout'),
            'grid' => __('Grid Layout'),
            'masonry' => __('Masonry Layout'),
            'fullwidth' => __('Full Width Layout'),
            'packery' => __('Packery Layout'),
            'sidebar-canvas' => __('Filter Canvas')
        ];
    }
}