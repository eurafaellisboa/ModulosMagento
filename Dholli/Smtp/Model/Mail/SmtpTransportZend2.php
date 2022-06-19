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

use Dholi\Smtp\Helper\Data;

class SmtpTransportZend2 {

	const ENCODING = 'utf-8';

	private $emailSettings;

	private $zendMailTransportSmtp2Factory;

	public function __construct(Data $emailSettings,
	                            ZendMailTransportSmtp2Factory $zendMailTransportSmtp2Factory) {
		$this->emailSettings = $emailSettings;
		$this->zendMailTransportSmtp2Factory = $zendMailTransportSmtp2Factory;
	}

	public function send($message, $storeId) {
		$smtpOptions = $this->emailSettings->getSmtpOptions($storeId);
		$smtp = $this->zendMailTransportSmtp2Factory->create($smtpOptions);
		$message->setEncoding(self::ENCODING);
		$smtp->send($message);
	}
}
