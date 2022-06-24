<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Margin;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['0' => __('0px'), '10' => __('10px'), '20' => __('20px'), '30' => __('30px'), '40' => __('40px')];
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
