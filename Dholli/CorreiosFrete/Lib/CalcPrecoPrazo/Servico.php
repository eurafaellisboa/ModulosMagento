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

class Servico {

	public $Codigo = null;

	public $Valor = null;

	public $PrazoEntrega = null;

	public $ValorMaoPropria = null;

	public $ValorAvisoRecebimento = null;

	public $ValorValorDeclarado = null;

	public $EntregaDomiciliar = null;

	public $EntregaSabado = null;

	public $Erro = null;

	public $MsgErro = null;

	public $ValorSemAdicionais = null;

	public $obsFim = null;

	public function __construct($Codigo, $Valor, $PrazoEntrega, $ValorMaoPropria, $ValorAvisoRecebimento, $ValorValorDeclarado, $EntregaDomiciliar, $EntregaSabado, $Erro, $MsgErro, $ValorSemAdicionais, $obsFim) {
		$this->Codigo = $Codigo;
		$this->Valor = $Valor;
		$this->PrazoEntrega = $PrazoEntrega;
		$this->ValorMaoPropria = $ValorMaoPropria;
		$this->ValorAvisoRecebimento = $ValorAvisoRecebimento;
		$this->ValorValorDeclarado = $ValorValorDeclarado;
		$this->EntregaDomiciliar = $EntregaDomiciliar;
		$this->EntregaSabado = $EntregaSabado;
		$this->Erro = $Erro;
		$this->MsgErro = $MsgErro;
		$this->ValorSemAdicionais = $ValorSemAdicionais;
		$this->obsFim = $obsFim;
	}

}
