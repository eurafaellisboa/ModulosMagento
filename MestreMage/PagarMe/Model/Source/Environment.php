<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\PagarMe\Model\Source;

class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const SANDBOX = 'sandbox';
    const PRODUCTION = 'production';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRODUCTION,
                'label' => __('Production')
            ],
            [
                'value' =>  self::SANDBOX,
                'label' =>__('Sandbox')
            ]
        ];
    }
}
