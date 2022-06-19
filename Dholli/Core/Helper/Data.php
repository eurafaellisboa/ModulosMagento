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

namespace Dholi\Core\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	public function __construct(\Magento\Framework\App\Helper\Context $context) {
		parent::__construct($context);
	}
}