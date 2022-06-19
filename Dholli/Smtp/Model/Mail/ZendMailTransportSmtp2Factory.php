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

use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;

class ZendMailTransportSmtp2Factory {
	public function create($smtpOptions) {
		return new Smtp($smtpOptions);
	}
}