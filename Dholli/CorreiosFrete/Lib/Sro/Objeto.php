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

class Objeto {

	/**
	 * @var string $numero
	 * @access public
	 */
	public $numero = null;

	/**
	 * @var string $sigla
	 * @access public
	 */
	public $sigla = null;

	/**
	 * @var string $nome
	 * @access public
	 */
	public $nome = null;

	/**
	 * @var string $categoria
	 * @access public
	 */
	public $categoria = null;

	/**
	 * @var string $erro
	 * @access public
	 */
	public $erro = null;

	/**
	 * @var Eventos $evento
	 * @access public
	 */
	public $evento = null;

	/**
	 * @param string $numero
	 * @param string $sigla
	 * @param string $nome
	 * @param string $categoria
	 * @param string $erro
	 * @param Eventos $evento
	 * @access public
	 */
	public function __construct($numero, $sigla, $nome, $categoria, $erro, $evento) {
		$this->numero = $numero;
		$this->sigla = $sigla;
		$this->nome = $nome;
		$this->categoria = $categoria;
		$this->erro = $erro;
		$this->evento = $evento;
	}
}
