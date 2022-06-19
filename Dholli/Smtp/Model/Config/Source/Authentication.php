<?php
/**
* 
* SMTP para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
namespace Dholi\Smtp\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Authentication implements ArrayInterface {

	public function toOptionArray() {
		$options = [
			[
				'value' => '',
				'label' => __('None')
			],
			[
				'value' => 'plain',
				'label' => __('Plain')
			],
			[
				'value' => 'login',
				'label' => __('Login')
			],
			[
				'value' => 'crammd5',
				'label' => __('Cram-MD5')
			],
		];

		return $options;
	}
}
