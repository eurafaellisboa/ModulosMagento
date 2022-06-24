<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace MGS\MollaTheme\Model\Config\Source;

class Template implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('Product default layout')], 
            ['value' => '1', 'label' => __('Full width')], 
            ['value' => '2', 'label' => __('Product with sticky info')], 
            ['value' => '6', 'label' => __('Product centered')]
        ];
    }
}
