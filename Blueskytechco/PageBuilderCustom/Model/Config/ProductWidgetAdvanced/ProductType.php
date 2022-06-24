<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\ProductWidgetAdvanced;

class ProductType implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['' => __('Please select'), 'best_seller' => __('Best Seller'), 'featured' => __('Featured'), 'most_viewed' => __('Most Viewed'), 'new_arrival' => __('New Arrival'), 'top_rate' => __('Top Rate'), 'on_sale' => __('On Sale'), 'trending' => __('Trending'), 'random' => __('Random')];
        $options = [];
        foreach ($arr as $key => $option) {
            $options[] =
                [
                    'value' => $key,
                    'label' => $option
                ];
        }
        return $options;
    }
}
