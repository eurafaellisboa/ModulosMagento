<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\Getnet\Model\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const SANDBOX = 'sandbox';
    const PRODUCTION = 'production';

    public function toOptionArray()
    {
        return [
            [
                'value' => "PRODUCTION",
                'label' => __('Production')
            ],
            [
                'value' =>  "SANDBOX",
                'label' =>__('Sandbox')
            ],
            [
                'value' =>  "HOMOLOG",
                'label' =>__('Homolog')
            ]
        ];
    }
}
