<?php
namespace Blueskytechco\QuickviewProduct\Model\Config\Action;

/**
 * @api
 * @since 100.0.2
 */
class Gallery implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'dots', 'label' => __('Dots')],
            ['value' => 'thumbs', 'label' => __('Thumbs')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['dots' => __('Dots'), 'thumbs' => __('Thumbs')];
    }
}
