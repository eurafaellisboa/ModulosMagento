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

use Dholi\CorreiosFrete\Model\Carrier;

class Servicos implements \Magento\Framework\Option\ArrayInterface {

	private $carrier;

	public function __construct(Carrier $carrier) {
		$this->carrier = $carrier;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray() {
		$options = [];
		foreach ($this->carrier->getCode('service') as $k => $v) {
			$options[] = ['value' => $k, 'label' => $v];
		}

		return $options;
	}
}