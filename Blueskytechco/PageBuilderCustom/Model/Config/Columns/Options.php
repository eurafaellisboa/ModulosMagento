<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Columns;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['2' => __('2 columns'), '3' => __('3 columns'), '4' => __('4 columns'), '5' => __('5 columns'), '6' => __('6 columns'), '7' => __('7 columns'), '8' => __('8 columns'), '9' => __('9 columns'), '10' => __('10 columns'), '11' => __('11 columns'), '12' => __('12 columns')];
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
