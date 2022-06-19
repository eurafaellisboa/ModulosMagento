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

use Eloquent\Enumeration\Exception\ExtendsConcreteException;
use Eloquent\Enumeration\Exception\UndefinedMemberExceptionInterface;

/**
 * Abstract base class for Java-style enumerations with a value.
 *
 * @api
 */
abstract class AbstractValueMultiton extends AbstractMultiton implements ValueMultitonInterface {

	private $value;

	/**
	 * Construct and register a new value multiton member.
	 *
	 * @param string $key The string key to associate with this member.
	 * @param mixed $value The value of this member.
	 *
	 * @throws ExtendsConcreteException If the constructed member has an invalid inheritance hierarchy.
	 * @api
	 *
	 */
	protected function __construct($key, $value) {
		parent::__construct($key);

		$this->value = $value;
	}

	/**
	 * Returns a single member by value.
	 *
	 * @param mixed $value The value associated with the member.
	 * @param bool|null $isCaseSensitive True if the search should be case sensitive.
	 *
	 * @return static                            The first member with the supplied value.
	 * @throws UndefinedMemberExceptionInterface If no associated member is found.
	 * @api
	 *
	 */
	final public static function memberByValue($value, $isCaseSensitive = null) {
		return static::memberBy('value', $value, $isCaseSensitive);
	}

	/**
	 * Returns a single member by value. Additionally returns a default if no
	 * associated member is found.
	 *
	 * @param mixed $value The value associated with the member.
	 * @param ValueMultitonInterface|null $default The default value to return.
	 * @param bool|null $isCaseSensitive True if the search should be case sensitive.
	 *
	 * @return static The first member with the supplied value, or the default value.
	 * @api
	 *
	 */
	final public static function memberByValueWithDefault(
		$value,
		ValueMultitonInterface $default = null,
		$isCaseSensitive = null
	) {
		return static::memberByWithDefault(
			'value',
			$value,
			$default,
			$isCaseSensitive
		);
	}

	/**
	 * Returns a single member by value. Additionally returns null if the
	 * supplied value is null.
	 *
	 * @param mixed|null $value The value associated with the member, or null.
	 * @param bool|null $isCaseSensitive True if the search should be case sensitive.
	 *
	 * @return static|null                       The first member with the supplied value, or null if the supplied value is null.
	 * @throws UndefinedMemberExceptionInterface If no associated member is found.
	 * @api
	 *
	 */
	final public static function memberOrNullByValue(
		$value,
		$isCaseSensitive = null
	) {
		return static::memberOrNullBy('value', $value, $isCaseSensitive);
	}

	/**
	 * Returns a set of members matching the supplied value.
	 *
	 * @param mixed $value The value associated with the members.
	 * @param bool|null $isCaseSensitive True if the search should be case sensitive.
	 *
	 * @return array<string,static> All members with the supplied value.
	 * @api
	 *
	 */
	final public static function membersByValue($value, $isCaseSensitive = null) {
		return static::membersBy('value', $value, $isCaseSensitive);
	}

	/**
	 * Returns the value of this member.
	 *
	 * @return mixed The value of this member.
	 * @api
	 *
	 */
	final public function value() {
		return $this->value;
	}
}
