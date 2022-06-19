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

class CalcPrecoPrazoWS extends \SoapClient {

	const TIMEOUT = '30';

	private static $classmap = array(
		'CalcPrecoPrazo' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazo',
		'CalcPrecoPrazoResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoResponse',
		'Resultado' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\Resultado',
		'Servico' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\Servico',
		'CalcPrecoPrazoData' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoData',
		'CalcPrecoPrazoDataResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoDataResponse',
		'CalcPrecoPrazoRestricao' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoRestricao',
		'CalcPrecoPrazoRestricaoResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoRestricaoResponse',
		'CalcPreco' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPreco',
		'CalcPrecoResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoResponse',
		'CalcPrecoData' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoData',
		'CalcPrecoDataResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoDataResponse',
		'CalcPrazo' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazo',
		'CalcPrazoResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoResponse',
		'CalcPrazoData' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoData',
		'CalcPrazoDataResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoDataResponse',
		'CalcPrazoRestricao' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoRestricao',
		'CalcPrazoRestricaoResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoRestricaoResponse',
		'CalcPrecoFAC' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoFAC',
		'CalcPrecoFACResponse' => '\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoFACResponse');

	public function __construct(array $options = array(), $wsdl = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?wsdl') {
		foreach (self::$classmap as $key => $value) {
			if (!isset($options['classmap'][$key])) {
				$options['classmap'][$key] = $value;
			}
		}
		ini_set('default_socket_timeout', self::TIMEOUT);
		parent::__construct($wsdl, $options);
	}

	public function CalcPrecoPrazo(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazo $parameters) {
		return $this->__soapCall('CalcPrecoPrazo', array($parameters));
	}

	public function CalcPrecoPrazoData(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoData $parameters) {
		return $this->__soapCall('CalcPrecoPrazoData', array($parameters));
	}

	public function CalcPrecoPrazoRestricao(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoPrazoRestricao $parameters) {
		return $this->__soapCall('CalcPrecoPrazoRestricao', array($parameters));
	}

	public function CalcPreco(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPreco $parameters) {
		return $this->__soapCall('CalcPreco', array($parameters));
	}

	public function CalcPrecoData(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoData $parameters) {
		return $this->__soapCall('CalcPrecoData', array($parameters));
	}

	public function CalcPrazo(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazo $parameters) {
		return $this->__soapCall('CalcPrazo', array($parameters));
	}

	public function CalcPrazoData(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoData $parameters) {
		return $this->__soapCall('CalcPrazoData', array($parameters));
	}

	public function CalcPrazoRestricao(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrazoRestricao $parameters) {
		return $this->__soapCall('CalcPrazoRestricao', array($parameters));
	}

	public function CalcPrecoFAC(\Dholi\CorreiosFrete\Lib\CalcPrecoPrazo\CalcPrecoFAC $parameters) {
		return $this->__soapCall('CalcPrecoFAC', array($parameters));
	}

}
