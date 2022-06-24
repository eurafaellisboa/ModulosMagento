<?php

namespace Blueskytechco\AjaxSuite\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class SuccessType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'popup', 'label' => __('Popup')], ['value' => 'minicart', 'label' => __('Show Minicart')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['minicart' => __('Show Minicart'), 'popup' => __('Show Popup')];
    }
}
