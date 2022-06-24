<?php
namespace Blueskytechco\SizeChart\Model\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => '1', 'label' => __('Abas')], ['value' => '2', 'label' => __('Popup')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['1' => __('Abas'), '2' => __('Popup')];
    }
}