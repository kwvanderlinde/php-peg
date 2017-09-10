<?php
namespace Kwv\Peg\Combinators\Helpers;


class FreezableArrayObject extends \ArrayObject
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

	public function offsetSet($index, $newVal)
	{
		if ($this->isFrozen())
		{
			throw new InstanceIsFrozenException('The array object has already been frozen.');
		}

		parent::offsetSet($index, $newVal);
	}

	public function offsetUnset($index)
	{
		if ($this->isFrozen())
		{
			throw new InstanceIsFrozenException('The array object has already been frozen.');
		}

		parent::offsetUnset($index);
	}
}