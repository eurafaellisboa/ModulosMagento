<?php
namespace Blueskytechco\SearchSuite\Model\Config\Action;

/**
 * @api
 * @since 100.0.2
 */
class SearchType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'defaut', 'label' => __('Defaut')],
            ['value' => 'dropdown', 'label' => __('Dropdown')],
            ['value' => 'popup', 'label' => __('Popup')],
            ['value' => 'canvas', 'label' => __('Canvas')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'defaut' => __('Defaut'),
            'dropdown' => __('Dropdown'),
            'popup' => __('Popup'),
            'canvas' => __('Canvas')
        ];
    }
}
