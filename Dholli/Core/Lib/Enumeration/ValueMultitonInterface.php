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

namespace Dholi\Core\Lib\Enumeration;

/**
 * The interface implemented by Java-style enumeration instances with a value.
 *
 * @api
 */
interface ValueMultitonInterface extends MultitonInterface {
	/**
	 * Returns the value of this member.
	 *
	 * @return mixed The value of this member.
	 * @api
	 *
	 */
	public function value();
}
