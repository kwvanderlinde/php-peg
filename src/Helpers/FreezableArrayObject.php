<?php
namespace Kwv\Peg\Helpers;

use ArrayObject;
use Base\Exceptions\LogicException;


/**
 * An `ArrayObject` implementation which can be frozen to prevent modifications.
 */
class FreezableArrayObject extends ArrayObject
{
	/**
	 * @var bool
	 *   `true` if and only if the object has been frozen. Initially `false`.
	 */
	private $frozen;

	/**
	 * Initialize the instance.
	 *
	 * The instance will begin as not frozen.
	 *
	 * @param array $input
	 *   The array to wrap.
	 */
	public function __construct(array $input = [])
	{
		parent::__construct($input);

		$this->frozen = false;
	}

	/**
	 * Freeze the object.
	 *
	 * Once this call completes, no future modifications to the object will be allowed.
	 *
	 * @return void
	 */
	public function freeze()
	{
		$this->frozen = true;
	}

	/**
	 * Determine whether the object is frozen.
	 *
	 * @return bool
	 *   `true` if and only if the object has been frozen.
	 */
	public function isFrozen()
	{
		return $this->frozen;
	}

	/**
	 * Check that the instance is not frozen.
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

	/**
	 * @inheritDoc
	 */
	public function offsetSet($index, $newVal)
	{
		$this->assertNotFrozen();

		parent::offsetSet($index, $newVal);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset($index)
	{
		$this->assertNotFrozen();

		parent::offsetUnset($index);
	}

	/**
	 * @inheritDoc
	 */
	public function asort()
	{
		$this->assertNotFrozen();

		parent::asort();
	}

	/**
	 * @inheritDoc
	 */
	public function ksort()
	{
		$this->assertNotFrozen();

		parent::ksort();
	}

	/**
	 * @inheritDoc
	 */
	public function natcasesort()
	{
		$this->assertNotFrozen();

		parent::natcasesort();
	}

	/**
	 * @inheritDoc
	 */
	public function natsort()
	{
		$this->assertNotFrozen();

		parent::natsort();
	}

	/**
	 * @inheritDoc
	 */
	public function uasort($cmp_function)
	{
		$this->assertNotFrozen();

		parent::uasort($cmp_function);
	}

	/**
	 * @inheritDoc
	 */
	public function uksort($cmp_function)
	{
		$this->assertNotFrozen();

		parent::uksort($cmp_function);
	}

	/**
	 * @inheritDoc
	 */
	public function exchangeArray($other)
	{
		$this->assertNotFrozen();

		parent::exchangeArray($other);
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize($serialized)
	{
		$this->assertNotFrozen();

		parent::unserialize($serialized);
	}
}