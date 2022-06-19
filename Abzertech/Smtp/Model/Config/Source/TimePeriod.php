<?php

namespace Abzertech\Smtp\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TimePeriod implements ArrayInterface
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
                ['value' => 'daily', 'label' => __('Daily')],
                ['value' => 'weekly', 'label' => __('Weekly')],
                ['value' => 'monthly', 'label' => __('Monthly')],
                ['value' => 'yearly', 'label' => __('Yearly')],
                ['value' => 'never', 'label' => __('Never')]
        ];
    }
}
