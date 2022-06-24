<?php
namespace Blueskytechco\Themeoption\Model\Config;

class BlogPageLayout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'sidebar-right', 'label' => __('Blog Right Sidebar')],
            ['value' => 'grid', 'label' => __('Blog Grid')],
            ['value' => 'masonry', 'label' => __('Blog Masonry')],
            ['value' => 'sidebar-left', 'label' => __('Blog Left Sidebar')]
        ];
    }

    public function toArray()
    {
        return [
            'sidebar-right' => __('Blog Right Sidebar'),
            'grid' => __('Blog Grid'),
            'masonry' => __('Blog Masonry'),
            'sidebar-left' => __('Blog Left Sidebar')
        ];
    }
}