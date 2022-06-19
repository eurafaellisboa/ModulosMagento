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

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class FlagCard implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'mastercard',
                'label' => __('Mastercard')
            ],
            [
                'value' =>  'visa',
                'label' =>__('Visa')
            ],
            [
                'value' =>  'amex',
                'label' =>__('American Express')
            ],
            [
                'value' =>  'diners',
                'label' =>__('Diners')
            ],
            [
                'value' =>  'elo',
                'label' =>__('Elo')
            ],
            [
                'value' =>  'hipercard',
                'label' =>__('Hipercard')
            ],
            [
                'value' =>  'discover',
                'label' =>__('Discover')
            ],
            [
                'value' =>  'jcb',
                'label' =>__('JCB')
            ],
            [
                'value' =>  'aura',
                'label' =>__('Aura')
            ]
        ];
    }
}
