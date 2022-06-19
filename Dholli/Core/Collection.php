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

namespace Dholi\Core;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfRangeException;

class Collection implements Countable, IteratorAggregate, ArrayAccess {

	protected $collection = [];

	public function add($value) {
		$this->collection[] = $value;
	}

	public function set($index, $value) {
		if ($index >= $this->count())
			throw new OutOfRangeException('Index out of range');

		$this->collection[$index] = $value;
	}

	public function remove($index) {
		if ($index >= $this->count())
			throw new OutOfRangeException('Index out of range');

		array_splice($this->collection, $index, 1);
	}

	public function get($index) {
		if ($index >= $this->count())
			throw new OutOfRangeException('Index out of range');

		return $this->collection[$index];
	}

	public function exists($index) {
		if ($index >= $this->count())
			return false;

		return true;
	}

	public function count() {
		return count($this->collection);
	}

	public function getIterator() {
		return new ArrayIterator($this->collection);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		$this->remove($offset);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetExists($offset) {
		return $this->exists($offset);
	}
}