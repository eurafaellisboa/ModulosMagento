<?php

/**

 *

 * @author      Jamacio Rocha

 * @copyright   2018 MestreMage (https://mestremage.com.br)

 * @license     https://mestremage.com.br Copyright

 *

 * @link        https://mestremage.com.br/

 */

namespace MestreMage\Cielo\Model\Source;



use Magento\Backend\App\Action;

use MestreMage\Cielo\API30\Ecommerce\Payment;



class TipoBoleto implements \Magento\Framework\Option\ArrayInterface

{



    public function toOptionArray()

    {

        return [

            Payment::PROVIDER_SIMULADO => __('Simulado'),

            Payment::PROVIDER_BRADESCO => __('Bradesco'),

            Payment::PROVIDER_BANCO_DO_BRASIL => __('Banco do Brasil'),

            'Bradesco2' => __('Bradesco Registrado'),

            'BancoDoBrasil2' => __('Banco do Brasil Registrado'),

        ];

    }

}