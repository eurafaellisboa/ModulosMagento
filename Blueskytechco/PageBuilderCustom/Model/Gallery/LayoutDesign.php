<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Gallery;

class LayoutDesign implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['grid' => __('Grid'), 'packery' => __('Packery'), 'carousel' => __('Carousel')];
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
