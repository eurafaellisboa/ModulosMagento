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



use Magento\Backend\App\Action;



class TipoBoleto implements \Magento\Framework\Option\ArrayInterface

{



    public function toOptionArray()

    {

        return [


            'Bradesco2' => __('Bradesco Registrado'),

            'BancoDoBrasil2' => __('Banco do Brasil Registrado'),

        ];

    }

}