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

class BuscaEventos {

	public $usuario = null;

	public $senha = null;

	public $tipo = null;

	public $resultado = null;

	public $lingua = null;

	public $objetos = null;

	/**
	 * BuscaEventos constructor.
	 * @param $usuario
	 * @param $senha
	 * @param $tipo
	 * @param $resultado
	 * @param $lingua
	 * @param $objetos
	 */
	public function __construct($usuario, $senha, $tipo, $resultado, $lingua, $objetos) {
		$this->usuario = $usuario;
		$this->senha = $senha;
		$this->tipo = $tipo;
		$this->resultado = $resultado;
		$this->lingua = $lingua;
		$this->objetos = $objetos;
	}
}