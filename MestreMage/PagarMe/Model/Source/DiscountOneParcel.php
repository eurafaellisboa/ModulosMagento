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

class DiscountOneParcel implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => '0',
                'label' => __('Não')
            ],
            [
                'value' =>  '1',
                'label' =>__('Fixo')
            ],
            [
                'value' =>  '2',
                'label' =>__('Porcento')
            ]
        ];
    }
}
