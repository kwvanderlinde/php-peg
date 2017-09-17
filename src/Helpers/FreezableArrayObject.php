<?php
namespace Kwv\Peg\Helpers;

use ArrayObject;
use Base\Exceptions\LogicException;


class FreezableArrayObject extends ArrayObject
{
	private $frozen;

	public function __construct(array $input = [])
	{
		parent::__construct($input);

		$this->frozen = false;
	}

	public function freeze()
	{
		$this->frozen = true;
	}

	public function isFrozen()
	{
		return $this->frozen;
	}

	/**
	 * Checks that the instance is not frozen.
	 *
	 * @return void
	 * @throws Fault
	     If the object is already frozen.
	 */
	protected function assertNotFrozen()
	{
		if ($this->isFrozen())
		{
			throw new LogicException('The array object has already been frozen.');
		}
	}

	public function offsetSet($index, $newVal)
	{
		$this->assertNotFrozen();

		parent::offsetSet($index, $newVal);
	}

	public function offsetUnset($index)
	{
		$this->assertNotFrozen();

		parent::offsetUnset($index);
	}

	public function asort()
	{
		$this->assertNotFrozen();

		parent::asort();
	}

	public function ksort()
	{
		$this->assertNotFrozen();

		parent::ksort();
	}

	public function natcasesort()
	{
		$this->assertNotFrozen();

		parent::natcasesort();
	}

	public function natsort()
	{
		$this->assertNotFrozen();

		parent::natsort();
	}

	public function uasort($cmp_function)
	{
		$this->assertNotFrozen();

		parent::uasort($cmp_function);
	}

	public function uksort($cmp_function)
	{
		$this->assertNotFrozen();

		parent::uksort($cmp_function);
	}

	public function exchangeArray($other)
	{
		$this->assertNotFrozen();

		parent::exchangeArray($other);
	}

	public function unserialize($serialized)
	{
		$this->assertNotFrozen();

		parent::unserialize($serialized);
	}
}