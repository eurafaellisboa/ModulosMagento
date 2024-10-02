<?php
namespace Digitaria\RDMarketing\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LogCleanupDays implements ArrayInterface
{
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('1 Day')],
            ['value' => '5', 'label' => __('5 Days')],
            ['value' => '7', 'label' => __('7 Days')],
            ['value' => '10', 'label' => __('10 Days')],
            ['value' => '15', 'label' => __('15 Days')],
            ['value' => '30', 'label' => __('30 Days')],
            ['value' => '60', 'label' => __('60 Days')],
        ];
    }
}
