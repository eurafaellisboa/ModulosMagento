<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Columns;

class Breakpoints implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['12' => __('1'), '6' => __('2'), '4' => __('3'), '3' => __('4'), '15' => __('5'), '2' => __('6')];
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