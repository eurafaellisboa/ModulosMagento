<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Gallery;

class ActionClick implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['photo_swipe' => __('Photo Swipe'), 'open_link' => __('Open Link')];
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
