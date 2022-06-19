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
namespace Dholi\Smtp\Model\Mail;

class ZendMailTransportSmtp1Factory {
	public function create($host, $config) {
		return new \Zend_Mail_Transport_Smtp($host, $config);
	}
}