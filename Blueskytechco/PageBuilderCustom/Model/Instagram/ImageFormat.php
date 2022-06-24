<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Instagram;

class ImageFormat implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['' => __('Please select'), 'square' => __('Square'), 'circle' => __('Circle')];
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
