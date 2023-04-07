<?php
/**
 *
 */
namespace Magento\ItauPix\Model\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const SANDBOX = 'gw-dev-app-key';
    const PRODUCTION = 'gw-app-key';

    public function toOptionArray()
    {
        return [
            [
                'value' =>  self::SANDBOX,
                'label' =>__('Sandbox')
            ],
            [
                'value' => self::PRODUCTION,
                'label' => __('Production')
            ]
        ];
    }
}
