<?php
/**
* 
* Core para Magento 2
* 
* @category     Dholi
* @package      Modulo Core
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\Core\Enumeration;

use Dholi\Core\Lib\Enumeration\AbstractMultiton;

class HttpStatus extends AbstractMultiton {

	public function getCode(): string {
		return $this->code;
	}

	protected static function initializeMembers() {
		new static('OK', '200');
		new static('CREATED', '201');
		new static('BAD_REQUEST', '400');
		new static('UNAUTHORIZED', '401');
		new static('FORBIDDEN', '403');
		new static('NOT_FOUND', '404');
		new static('UNPROCESSABLE_ENTITY', '422');
		new static('INTERNAL_SERVER_ERROR', '500');
		new static('BAD_GATEWAY', '502');
	}

	protected function __construct($key, $code) {
		parent::__construct($key);

		$this->code = $code;
	}

	private $code;
}