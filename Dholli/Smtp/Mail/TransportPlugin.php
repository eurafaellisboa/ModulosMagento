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

use Dholi\Smtp\Helper\Data;
use Dholi\Smtp\Model\Mail\SmtpTransportAdapter;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

class TransportPlugin {

	private $logger;

	private $smtpTransportAdapter;

	private $helper;

	private $registry;

	public function __construct(LoggerInterface $logger,
	                            SmtpTransportAdapter $smtpTransportAdapter,
	                            Data $helper,
	                            Registry $registry) {

		$this->logger = $logger;
		$this->smtpTransportAdapter = $smtpTransportAdapter;
		$this->helper = $helper;
		$this->registry = $registry;
	}

	public function aroundSendMessage(TransportInterface $subject, \Closure $proceed) {
		$storeId = $this->registry->registry(TransportBuilderPlugin::TRANSPORT_BUILDER_PLUGIN_STORE_ID);
		if ($this->helper->isEnabled($storeId)) {
			try {
				$this->smtpTransportAdapter->send($subject, $storeId);
			} catch (\Exception $e) {
				$this->logger->error("TransportPlugin send exception: " . $e->getMessage());
				return $proceed();
			}
		} else {
			return $proceed();
		}
	}
}