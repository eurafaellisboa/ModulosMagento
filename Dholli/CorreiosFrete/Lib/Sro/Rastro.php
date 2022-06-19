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

class Rastro extends \SoapClient {

	/**
	 * @var array $classmap The defined classes
	 * @access private
	 */
	private static $classmap = array(
		'buscaEventosLista' => '\Dholi\CorreiosFrete\Lib\Sro\BuscaEventosLista',
		'buscaEventosListaResponse' => '\Dholi\CorreiosFrete\Lib\Sro\BuscaEventosListaResponse',
		'sroxml' => '\Dholi\CorreiosFrete\Lib\Sro\Sroxml',
		'objeto' => '\Dholi\CorreiosFrete\Lib\Sro\Objeto',
		'eventos' => '\Dholi\CorreiosFrete\Lib\Sro\Eventos',
		'destinos' => '\Dholi\CorreiosFrete\Lib\Sro\Destinos',
		'enderecoMobile' => '\Dholi\CorreiosFrete\Lib\Sro\EnderecoMobile',
		'buscaEventos' => '\Dholi\CorreiosFrete\Lib\Sro\BuscaEventos',
		'buscaEventosResponse' => '\Dholi\CorreiosFrete\Lib\Sro\BuscaEventosResponse');

	/**
	 * @param array $options A array of config values
	 * @param string $wsdl The wsdl file to use
	 * @access public
	 */
	public function __construct(array $options = array(), $wsdl = 'http://webservice.correios.com.br/service/rastro/Rastro.wsdl') {
		foreach (self::$classmap as $key => $value) {
			if (!isset($options['classmap'][$key])) {
				$options['classmap'][$key] = $value;
			}
		}

		parent::__construct($wsdl, $options);
	}

	/**
	 * @param BuscaEventos $parameters
	 * @access public
	 * @return BuscaEventosResponse
	 */
	public function buscaEventos(BuscaEventos $parameters) {
		return $this->__soapCall('buscaEventos', array($parameters));
	}

	/**
	 * @param BuscaEventosLista $parameters
	 * @access public
	 * @return BuscaEventosListaResponse
	 */
	public function buscaEventosLista(BuscaEventosLista $parameters) {
		return $this->__soapCall('buscaEventosLista', array($parameters));
	}

}
