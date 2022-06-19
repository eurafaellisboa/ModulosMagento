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
 * The interface implemented by Java-style enumeration instances.
 *
 * @api
 */
interface MultitonInterface {
	/**
	 * Returns the string key of this member.
	 *
	 * @return string The associated string key of this member.
	 * @api
	 *
	 */
	public function key();

	/**
	 * Check if this member is in the specified list of members.
	 *
	 * @param MultitonInterface $a The first member to check.
	 * @param MultitonInterface $b The second member to check.
	 * @param MultitonInterface $c,... Additional members to check.
	 *
	 * @return bool True if this member is in the specified list of members.
	 * @api
	 *
	 */
	public function anyOf(MultitonInterface $a, MultitonInterface $b);

	/**
	 * Check if this member is in the specified list of members.
	 *
	 * @param array<MultitonInterface> $values An array of members to search.
	 *
	 * @return bool True if this member is in the specified list of members.
	 * @api
	 *
	 */
	public function anyOfArray(array $values);

	/**
	 * Returns a string representation of this member.
	 *
	 * @return string
	 * @api
	 *
	 * Unless overridden, this is simply the string key.
	 *
	 */
	public function __toString();
}
