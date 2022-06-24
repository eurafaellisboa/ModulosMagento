<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Blueskytechco\MenuBuilder\Model\Config\Source;

class Animation implements \Magento\Framework\Data\OptionSourceInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'unfold', 'label' => __('Unfold')],
            ['value' => 'fading', 'label' => __('Fading')],
            ['value' => 'down_to_up', 'label' => __('Down to Up')],
            ['value' => 'dropdown', 'label' => __('Dropdown')]
        ];
    }
}
