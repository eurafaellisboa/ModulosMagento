<?php

namespace MGS\Blog\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Show implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'blog_grid',
                'label' => __('Blog Classic')
            ],
            [
                'value' => 'blog_masorry',
                'label' => __('Blog Masorry')
            ]
        ];
    }

}
