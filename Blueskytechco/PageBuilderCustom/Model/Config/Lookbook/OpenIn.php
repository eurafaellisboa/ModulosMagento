<?php
declare(strict_types=1);

namespace Blueskytechco\PageBuilderCustom\Model\Config\Lookbook;

class OpenIn implements \Magento\Framework\Data\OptionSourceInterface
{

    public function toOptionArray(): array
    {
        $arr = ['quickview' => __('Quickview'), 'popup' => __('Popup Boxes')];
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