<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg\Combinators\Helpers;

use Kwv\Peg\Combinators\Helpers\FreezableArrayObject;
use Kwv\Peg\Combinators\Helpers\InstanceIsFrozenException;


/**
 * @coversDefaultClass Kwv\Peg\Combinators\Helpers\FreezableArrayObject
 */
class FreezableArrayObjectTest extends \Tests\Kwv\Peg\TestCase
{
	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the constructor.
	 * @param array|null $expectedException
	 *   If an exception is expected, the type of the exception to expect. Otherwise, `null` if no exception is expected.
	 *
	 * @covers ::__construct
	 * @dataProvider constructProvider
	 */
	public function testConstruct(array $args, array $expectedException = null)
	{
		if (null !== $expectedException)
		{
			$this->expectFullException(...$expectedException);
		}

		$instance = new FreezableArrayObject(...$args);
		$this->assertInstanceOf(FreezableArrayObject::class, $instance);
		$this->assertInstanceOf(\ArrayObject::class, $instance, 'FreezableArrayObject must extend ArrayObject.');
	}

	public function constructProvider()
	{
		return [
			'Can be constructed with no arguments' => [ [] ],
			'Can be constructed with an array' => [ [ [ 1, 2, 3 ] ] ],
			'Cannot be constructed with an object' => [ [ new \stdClass ], [ \TypeError::class ] ],
			'Cannot be constructed with null' => [ [ null ], [ \TypeError::class ] ],
		];
	}

	/**
	 * @dataProvider instanceWrapsInputProvider
	 */
	public function testThatInstanceWrapsInput(array $input = null, array $expectedException = null)
	{
		if (null !== $expectedException)
		{
			$this->expectFullException(...$expectedException);
		}
		$args = null === $input ? [] : [ $input ];

		$instance = new FreezableArrayObject(...$args);
		$elements = $instance->getArrayCopy();
		$this->assertSame(null === $input ? [] : $input, $elements, 'Input is preserved as elements of the array object.');
	}

	public function instanceWrapsInputProvider()
	{
		return [
			'No input' => [ null ],
			'Empty input' => [ [] ],
			'Simple sequence' => [ [ 1, 2, 3 ] ],
		];
	}

	public function testThatWeCanCheckIfAnObjectIsFrozen()
	{
		$instance = new FreezableArrayObject();
		$this->assertSame(false, $instance->isFrozen(), 'Instance does not report being frozen before freeze.');
		$instance->freeze();
		$this->assertSame(true, $instance->isFrozen(), 'Instance reports being frozen after freeze.');
		$instance->freeze();
		$this->assertSame(true, $instance->isFrozen(), 'Instance still reports being frozen after a second freeze.');
	}

	public function testThatInstanceIsInitiallyWritable()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$newValue = 5;
		$instance['a'] = $newValue;
		$this->assertSame($newValue, $instance['a'], 'The write has occurred.');
	}

	public function testThatWeCannotWriteToAFrozenInstance()
	{
		$this->expectException(InstanceIsFrozenException::class);

		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		// Now we freeze.
		$instance->freeze();
		// Now we can't write to it.
		$instance['a'] = 3;
	}

	public function testThatWeCanUnsetElementsOfANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		unset($instance['a']);
		$this->assertNotContains('a', $instance, 'The unset has occurred.');
	}

	public function testThatWeCannotUnsetElementsOfAFrozenInstance()
	{
		$this->expectException(InstanceIsFrozenException::class);

		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		// Now we freeze.
		$instance->freeze();
		// Now we can't unset.
		unset($instance['a']);
	}
}