<?php
namespace Blueskytechco\SearchSuite\Model\Config\Action;

/**
 * @api
 * @since 100.0.2
 */
class CategoryType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'list', 'label' => __('List')],
            ['value' => 'dropdown', 'label' => __('Dropdown')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['list' => __('List'), 'dropdown' => __('Dropdown')];
    }
}
