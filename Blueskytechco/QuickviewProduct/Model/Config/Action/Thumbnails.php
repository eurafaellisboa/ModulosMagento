<?php
namespace Blueskytechco\QuickviewProduct\Model\Config\Action;

/**
 * @api
 * @since 100.0.2
 */
class Thumbnails implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'left', 'label' => __('Left')],
            ['value' => 'right', 'label' => __('Right')],
            ['value' => 'bottom', 'label' => __('Bottom')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['left' => __('Left'), 'right' => __('Right'), 'bottom' => __('Bottom')];
    }
}
