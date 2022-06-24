<?php

namespace MGS\Portfolio\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Show implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'portfolio_grid',
                'label' => __('Grid')
            ],
            [
                'value' => 'portfolio_carousel',
                'label' => __('Carousel')
            ],
            [
                'value' => 'portfolio_masonry',
                'label' => __('Masonry')
            ]
        ];
    }

}
