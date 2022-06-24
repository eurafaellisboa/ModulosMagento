<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Rows;

class Slick implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['1' => __('1'), '2' => __('2'), '3' => __('3'), '4' => __('4'), '5' => __('5'), '6' => __('6')];
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