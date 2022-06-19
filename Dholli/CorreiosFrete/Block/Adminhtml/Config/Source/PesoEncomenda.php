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

namespace Dholi\CorreiosFrete\Block\Adminhtml\Config\Source;

class PesoEncomenda implements \Magento\Framework\Option\ArrayInterface {

	const WEIGHT_GR = 'gr';
	const WEIGHT_KG = 'kg';

	public function toOptionArray() {
		return [
			['value' => self::WEIGHT_GR, 'label' => __('Gramas')],
			['value' => self::WEIGHT_KG, 'label' => __('Kilos')],
		];
	}
}