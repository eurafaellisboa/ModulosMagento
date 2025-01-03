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

class PaymentAction implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize')
            ],
            [
                'value' =>  AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' =>__('Authorize and Capture')
            ]
        ];
    }
}
