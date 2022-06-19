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

class FormatoEmbalagem implements \Magento\Framework\Option\ArrayInterface {

	public function toOptionArray() {
		return [
			['value' => '1', 'label' => __('Caixa/Pacote')],
			['value' => '2', 'label' => __('Rolo/Prisma')],
			['value' => '3', 'label' => __('Envelope')]
		];
	}
}