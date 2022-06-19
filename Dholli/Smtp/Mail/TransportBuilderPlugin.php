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
namespace Dholi\Smtp\Mail;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Registry;

class TransportBuilderPlugin {

	const TRANSPORT_BUILDER_PLUGIN_STORE_ID = 'transportBuilderPluginStoreId';

	private $registry;

	public function __construct(Registry $registry) {
		$this->registry = $registry;
	}

	public function beforeSetTemplateOptions(TransportBuilder $transportBuilder, $templateOptions) {
		if (null !== $this->registry->registry(self::TRANSPORT_BUILDER_PLUGIN_STORE_ID)) {
			$this->registry->unregister(self::TRANSPORT_BUILDER_PLUGIN_STORE_ID);
		}

		$this->registry->register(self::TRANSPORT_BUILDER_PLUGIN_STORE_ID, $templateOptions['store']);
		return null;
	}
}
