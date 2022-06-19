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

namespace Dholi\Core\Lib\Enumeration\Exception;

/**
 * The interface implemented by exceptions that are thrown when an undefined
 * member is requested.
 *
 * @api
 */
interface UndefinedMemberExceptionInterface {
	/**
	 * Get the class name.
	 *
	 * @return string The class name.
	 * @api
	 *
	 */
	public function className();

	/**
	 * Get the property name.
	 *
	 * @return string The property name.
	 * @api
	 *
	 */
	public function property();

	/**
	 * Get the value of the property used to search for the member.
	 *
	 * @return mixed The value.
	 * @api
	 *
	 */
	public function value();
}
