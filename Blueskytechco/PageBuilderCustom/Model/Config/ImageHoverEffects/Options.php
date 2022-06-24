<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\ImageHoverEffects;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['none' => __('Please select'), 'zoom' => __('Zoom'), 'zoom-overlay' => __('Zoom Overlay'), 'box-effect' => __('Box Effect'), 'overlay' => __('Overlay')];
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
