<?php
/**
* 
* MÃ¡scaras PortuguÃªs Brasil para Magento 2
* 
* @category     Dholi
* @package      Modulo MaskBr
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\MaskBr\Block;

use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\Template\Context;

class Mask extends \Magento\Framework\View\Element\Template {

	private $localeResolver;

	public function __construct(Context $context,
	                            ResolverInterface $localeResolver) {
		$this->localeResolver = $localeResolver;

		parent::__construct($context);
	}

	private function getStoreId() {
		return $this->storeManager->getStore()->getId();
	}

	private function getLanguage() {
		return $this->localeResolver->getLocale();
	}

	public function canApplyMasks() {
		return ($this->getLanguage() == 'pt_BR');
	}
}