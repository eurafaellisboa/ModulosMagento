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

class CalcPrecoPrazoResponse {

	public $CalcPrecoPrazoResult = null;

	public function __construct($CalcPrecoPrazoResult) {
		$this->CalcPrecoPrazoResult = $CalcPrecoPrazoResult;
	}

}
