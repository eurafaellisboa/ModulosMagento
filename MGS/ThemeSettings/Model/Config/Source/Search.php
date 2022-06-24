<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace MGS\ThemeSettings\Model\Config\Source;

class Search implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
			['value' => 'visible', 'label' => __('Always show search box')], 
			['value' => 'icon-left', 'label' => __('Hide search box and show in left when icon is clicked')],
			['value' => 'icon-bottom', 'label' => __('Hide search box and show in bottom when icon is clicked')],
			['value' => 'icon-popup', 'label' => __('Hide search box and show on popup when icon is clicked')]
		];
    }
}
