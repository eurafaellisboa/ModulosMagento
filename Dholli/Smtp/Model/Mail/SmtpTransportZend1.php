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

class SmtpTransportZend1 {

	private $emailSettings;

	private $zendMailTransportSmtp1Factory;

	public function __construct(Data $emailSettings,
	                            ZendMailTransportSmtp1Factory $zendMailTransportSmtp1Factory) {
		$this->emailSettings = $emailSettings;
		$this->zendMailTransportSmtp1Factory = $zendMailTransportSmtp1Factory;
	}

	public function send($message, $storeId) {
		$host = $this->emailSettings->getSmtpHost($storeId);
		$config = $this->emailSettings->getTransportConfig($storeId);

		$smtp = $this->zendMailTransportSmtp1Factory->create($host, $config);
		$smtp->send($message);
	}
}