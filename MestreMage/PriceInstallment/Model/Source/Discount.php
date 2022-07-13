<?php
/**
 *
 * @author      Jamacio Rocha
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\PriceInstallment\Model\Source;

class Discount implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('fixed')
            ],
            [
                'value' =>  2,
                'label' =>__('Percents')
            ]
        ];
    }
}
