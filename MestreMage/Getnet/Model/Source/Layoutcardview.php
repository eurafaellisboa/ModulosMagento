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

use Magento\Framework\Option\ArrayInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class Layoutcardview implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Layout default')
            ],
            [
                'value' =>  2,
                'label' =>__('Layout custom')
            ]
        ];
    }
}
