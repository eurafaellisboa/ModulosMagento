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
declare(strict_types=1);

namespace Dholi\Smtp\Mail;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Mail\MessageInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Zend\Mail\Message as ZendMessage;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class Transport implements \Magento\Framework\Mail\TransportInterface {

	private $logger;

	private $scopeConfig;

	private $storeManager;

	private $encryptor;

	private $transport;

	private $message;

	public function __construct(MessageInterface $message,
	                            LoggerInterface $logger,
	                            ScopeConfigInterface $scopeConfig,
	                            StoreManagerInterface $storeManager,
	                            EncryptorInterface $encryptor) {

		$this->message = $message;
		$this->logger = $logger;
		$this->scopeConfig = $scopeConfig;
		$this->storeManager = $storeManager;
		$this->encryptor = $encryptor;

		$storeId = $this->storeManager->getStore()->getId();
		$smtpHost = $this->scopeConfig->getValue('system/smtp/host', ScopeInterface::SCOPE_STORE, $storeId);
		$port = $this->scopeConfig->getValue('system/smtp/port', ScopeInterface::SCOPE_STORE, $storeId);
		$auth = $this->scopeConfig->getValue('system/smtp/authentication', ScopeInterface::SCOPE_STORE, $storeId);

		$this->transport = new SmtpTransport();
		$options = new SmtpOptions([
			'name' => $smtpHost,
			'host' => $smtpHost,
			'port' => $port
		]);
		if (!empty($auth)) {
			$username = $this->scopeConfig->getValue('system/smtp/username', ScopeInterface::SCOPE_STORE, $storeId);
			$password = $this->encryptor->decrypt($this->scopeConfig->getValue('system/smtp/password', ScopeInterface::SCOPE_STORE, $storeId));
			$protocol = $this->scopeConfig->getValue('system/smtp/protocol', ScopeInterface::SCOPE_STORE, $storeId);

			$options->setConnectionClass($auth);
			$options->setConnectionConfig(['username' => $username, 'password' => $password, 'ssl' => $protocol]);
		}
		$this->transport->setOptions($options);
	}

	/**
	 * @inheritdoc
	 */
	public function sendMessage() {
		try {
			$this->transport->send(ZendMessage::fromString($this->getMessage()->getRawMessage()));
		} catch (\Exception $e) {
			throw new MailException(new Phrase($e->getMessage()), $e);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getMessage() {
		return $this->message;
	}
}