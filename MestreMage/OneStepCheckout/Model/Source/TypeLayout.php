<?php
namespace MestreMage\OneStepCheckout\Model\Source;

use Magento\Backend\App\Action;

class TypeLayout implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return [
            '1' => __('Layout 1'),
            '2' => __('Layout 2'),
            '3' => __('Layout 3'),
        ];
    }
}