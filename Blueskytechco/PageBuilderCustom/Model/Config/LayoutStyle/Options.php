<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\LayoutStyle;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['default' => __('Style 1 - Default'), 'daily__style-2' => __('Style 2'), 'daily__style-3' => __('Style 3'), ];
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