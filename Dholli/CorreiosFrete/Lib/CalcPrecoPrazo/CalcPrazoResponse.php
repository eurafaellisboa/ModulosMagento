<?php
/**
* 
* Frente com Correios para Magento 2
* 
* @category     Dholi
* @package      Modulo Frente com Correios
* @copyright    Copyright (c) 2020 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\CorreiosFrete\Lib\CalcPrecoPrazo;

class CalcPrazoResponse {

	/**
	 * @var cResultado $CalcPrazoResult
	 * @access public
	 */
	public $CalcPrazoResult = null;

	/**
	 * @param cResultado $CalcPrazoResult
	 * @access public
	 */
	public function __construct($CalcPrazoResult) {
		$this->CalcPrazoResult = $CalcPrazoResult;
	}

}
