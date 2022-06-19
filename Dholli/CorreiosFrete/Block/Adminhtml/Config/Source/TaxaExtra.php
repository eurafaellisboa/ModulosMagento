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

class TaxaExtra implements \Magento\Framework\Option\ArrayInterface {

	public function toOptionArray() {
		return [
			['value' => '0', 'label' => __('Não')],
			['value' => '1', 'label' => __('Em percentual')],
			['value' => '2', 'label' => __('Em valor')]
		];
	}
}