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

namespace Dholi\CorreiosFrete\Lib\Sro;

class BuscaEventosResponse {

	/**
	 * @var Sroxml $return
	 * @access public
	 */
	public $return = null;

	/**
	 * @param Sroxml $return
	 * @access public
	 */
	public function __construct(Sroxml $return) {
		$this->return = $return;
	}
}
