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

use Magento\Framework\Mail\TransportInterface;
use Zend\Mail\Message;
use Zend_Mail;
use Dholi\Smtp\Helper\Data;

class SmtpTransportAdapter {

	private $emailSettings;

	private $smtpTransportZendV1;

	private $smtpTransportZendV2;

	public function __construct(Data $emailSettings,
	                            SmtpTransportZend1 $smtpTransportZendV1,
	                            SmtpTransportZend2 $smtpTransportZendV2) {
		$this->emailSettings = $emailSettings;
		$this->smtpTransportZendV1 = $smtpTransportZendV1;
		$this->smtpTransportZendV2 = $smtpTransportZendV2;
	}

	public function send($subject, $storeId) {
		$message = $this->getMessage($subject);
		$this->sendMessage($message, $storeId);
	}

	private function getMessage($subject) {
		if (method_exists($subject, 'getMessage')) {
			$message = $subject->getMessage();
		} else {
			$message = $this->useReflectionToGetMessage($subject);
		}

		return $message;
	}

	private function useReflectionToGetMessage($subject) {
		$reflection = new \ReflectionClass($subject);
		$property = $reflection->getProperty('_message');
		$property->setAccessible(true);
		$message = $property->getValue($subject);

		return $message;
	}

	private function sendMessage($message, $storeId) {
		if ($message instanceof \Zend_Mail) {
			$this->smtpTransportZendV1->send($message, $storeId);
		} else {
			$this->smtpTransportZendV2->send(Message::fromString($message->getRawMessage()), $storeId);
		}
	}
}
