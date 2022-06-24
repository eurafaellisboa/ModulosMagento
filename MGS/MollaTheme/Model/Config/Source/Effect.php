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

class Effect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
			['value' => 'effect1', 'label' => __('Effect 1 - Text Align Center')], 
			['value' => 'effect2', 'label' => __('Effect 2 - Text Align Left')], 
			['value' => 'effect3', 'label' => __('Effect 3 - Text Align Center')], 
			['value' => 'effect4', 'label' => __('Effect 4 - Align Left & Action Dask')],
			['value' => 'effect5', 'label' => __('Effect 5 - Fashion')], 
			['value' => 'effect6', 'label' => __('Effect 6')],
			['value' => 'effect7', 'label' => __('Effect 7')]
		];
    }
}
