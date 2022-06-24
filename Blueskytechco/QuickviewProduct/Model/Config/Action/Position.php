<?php
namespace Blueskytechco\QuickviewProduct\Model\Config\Action;

/**
 * @api
 * @since 100.0.2
 */
class Position implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'append', 'label' => __('Append')],
            ['value' => 'after', 'label' => __('After')],
            ['value' => 'before', 'label' => __('Before')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['append' => __('Append'), 'after' => __('After'), 'before' => __('Before')];
    }
}
