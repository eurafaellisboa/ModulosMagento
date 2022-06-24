<?php
namespace Blueskytechco\Themeoption\Model\Config;

class DisplayThumbnails implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Product Default Thumbnails')],
            ['value' => 'bottom', 'label' => __('Product Bottom Thumbnails')],
            ['value' => 'right', 'label' => __('Product Right Thumbnails')],
            ['value' => 'none', 'label' => __('Product Without Thumbnails')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('Product Default Thumbnails'),
            'bottom' => __('Product Bottom Thumbnails'),
            'right' => __('Product Right Thumbnails'),
            'none' => __('Product Without Thumbnails')
        ];
    }
}