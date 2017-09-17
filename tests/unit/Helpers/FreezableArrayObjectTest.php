<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg\Helpers;

use ArrayObject;
use stdClass;
use TypeError;
use Base\Exceptions\Fault;
use Kwv\Peg\Helpers\FreezableArrayObject;


/**
 * @coversDefaultClass Kwv\Peg\Helpers\FreezableArrayObject
 */
class FreezableArrayObjectTest extends \Tests\Kwv\Peg\TestCase
{
	/**
	 * @coversNothing
	 */
	public function testExtendsArrayObject()
	{
		$this->assertSubtypeOf(ArrayObject::class, FreezableArrayObject::class);
	}

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
	}

	public function constructProvider()
	{
		return [
			'Can be constructed with no arguments' => [ [] ],
			'Can be constructed with an array' => [ [ [ 1, 2, 3 ] ] ],
			'Cannot be constructed with an object' => [ [ new stdClass ], [ TypeError::class ] ],
			'Cannot be constructed with null' => [ [ null ], [ TypeError::class ] ],
		];
	}

	/**
	 * @covers ::assertNotFrozen
	 */
	public function testThatAsertNotFrozenDoesNotThrownIfNotFrozen()
	{
		$instance = new FreezableArrayObject();

		$method = new \ReflectionMethod($instance, 'assertNotFrozen');
		$method->setAccessible(true);
		$result = $method->invoke($instance);

		$this->assertSame(null, $result, 'assertNotFrozen does not return a value.');
	}

	/**
	 * @covers ::assertNotFrozen
	 */
	public function testThatAsertNotFrozenDoesThrowsIfNotFrozen()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject();
		$instance->freeze();

		$method = new \ReflectionMethod($instance, 'assertNotFrozen');
		$method->setAccessible(true);
		$method->invoke($instance);
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

	/**
	 * @covers ::isFrozen
	 * @covers ::freeze
	 */
	public function testThatWeCanCheckIfAnObjectIsFrozen()
	{
		$instance = new FreezableArrayObject();
		$this->assertSame(false, $instance->isFrozen(), 'Instance does not report being frozen before freeze.');
		$instance->freeze();
		$this->assertSame(true, $instance->isFrozen(), 'Instance reports being frozen after freeze.');
		$instance->freeze();
		$this->assertSame(true, $instance->isFrozen(), 'Instance still reports being frozen after a second freeze.');
	}

	/**
	 * @covers ::offsetGet
	 */
	public function testThatWeCanAccessKeys()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$this->assertSame(3, $instance['c'], 'We can access the key and it is associated with the right value.');
	}

	/**
	 * @covers ::offsetGet
	 */
	public function testThatWeCanAccessKeysOfAFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$instance->freeze();
		$this->assertSame(3, $instance['c'], 'We can access the key and it is associated with the right value.');
	}

	/**
	 * @covers ::offsetSet
	 */
	public function testThatInstanceIsInitiallyWritable()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$newValue = 5;
		$instance['a'] = $newValue;
		$this->assertSame($newValue, $instance['a'], 'The write has occurred.');
	}

	/**
	 * @covers ::offsetSet
	 */
	public function testThatWeCannotWriteToAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$instance->freeze();
		// Now we can't write to it.
		$instance['a'] = 3;
	}

	/**
	 * @covers ::offsetUnset
	 */
	public function testThatWeCanUnsetElementsOfANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		unset($instance['a']);
		$this->assertArrayNotHasKey('a', $instance, 'The unset has occurred.');
	}

	/**
	 * @covers ::offsetUnset
	 */
	public function testThatWeCannotUnsetElementsOfAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$instance->freeze();
		// Now we can't unset.
		unset($instance['a']);
	}

	/**
	 * @covers ::append
	 */
	public function testThatWeCanAppendToANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		// Now we can append.
		$instance->append('test');
		$this->assertContains('test', $instance, 'The append occurred.');
	}

	/**
	 * @covers ::append
	 */
	public function testThatWeCannotAppendToAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 1, 'b' => 2, 'c' => 3 ]);
		$instance->freeze();
		// Now we can't append.
		$instance->append('test');
	}

	/**
	 * @covers ::asort
	 */
	public function testThatWeCanAsortANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 3, 'b' => 1, 'c' => 2 ]);
		// Now we can sort.
		$instance->asort();
		$this->assertSame([ 'b' => 1, 'c' => 2, 'a' => 3 ], $instance->getArrayCopy(), 'The asort occurred.');
	}

	/**
	 * @covers ::asort
	 */
	public function testThatWeCannotAsortAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 3, 'b' => 1, 'c' => 2 ]);
		$instance->freeze();
		// Now we can't sort.
		$instance->asort();
	}

	/**
	 * @covers ::ksort
	 */
	public function testThatWeCanKsortANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'b' => 3, 'c' => 1, 'a' => 2 ]);
		// Now we can sort.
		$instance->ksort();
		$this->assertSame([ 'a' => 2, 'b' => 3, 'c' => 1 ], $instance->getArrayCopy(), 'The ksort occurred.');
	}

	/**
	 * @covers ::ksort
	 */
	public function testThatWeCannotKsortAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'b' => 3, 'c' => 1, 'a' => 2 ]);
		$instance->freeze();
		// Now we can't sort.
		$instance->ksort();
	}

	/**
	 * @covers ::natcasesort
	 */
	public function testThatWeCanNatcasesortANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 'img12.png', 'b' => 'img01.png', 'c' => 'img2.png' ]);
		// Now we can sort.
		$instance->natcasesort();
		$this->assertSame([ 'b' => 'img01.png', 'c' => 'img2.png', 'a' => 'img12.png' ], $instance->getArrayCopy(), 'The natcasesort occurred.');
	}

	/**
	 * @covers ::natcasesort
	 */
	public function testThatWeCannotNatcasesortAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 'img12.png', 'b' => 'img01.png', 'c' => 'img2.png' ]);
		$instance->freeze();
		// Now we can't sort.
		$instance->natcasesort();
	}

	/**
	 * @covers ::natsort
	 */
	public function testThatWeCanNatsortANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 'img12.png', 'b' => 'img01.png', 'c' => 'img2.png' ]);
		// Now we can sort.
		$instance->natsort();
		$this->assertSame([ 'b' => 'img01.png', 'c' => 'img2.png', 'a' => 'img12.png' ], $instance->getArrayCopy(), 'The natsort occurred.');
	}

	/**
	 * @covers ::natsort
	 */
	public function testThatWeCannotNatsortAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 'img12.png', 'b' => 'img01.png', 'c' => 'img2.png' ]);
		$instance->freeze();
		// Now we can't sort.
		$instance->natsort();
	}

	/**
	 * @covers ::uasort
	 */
	public function testThatWeCanUasortANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 'Test', 'b' => 'testing', 'c' => 'test' ]);
		// Now we can sort.
		$instance->uasort(function ($lhs, $rhs) {
				 return -strcmp($lhs, $rhs);
			 });
		$this->assertSame([ 'b' => 'testing', 'c' => 'test', 'a' => 'Test' ], $instance->getArrayCopy(), 'The uasort occurred.');
	}

	/**
	 * @covers ::uasort
	 */
	public function testThatWeCannotUasortAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 'Test', 'b' => 'testing', 'c' => 'test' ]);
		$instance->freeze();
		// Now we can't sort.
		$instance->uasort(function ($lhs, $rhs) {
				 return -strcmp($lhs, $rhs);
			 });
	}

	/**
	 * @covers ::uksort
	 */
	public function testThatWeCanUksortANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'Test' => 1, 'testing' => 2, 'test' => 3 ]);
		// Now we can sort.
		$instance->uksort(function ($lhs, $rhs) {
				 return -strcmp($lhs, $rhs);
			 });
		$this->assertSame([ 'testing' => 2, 'test' => 3, 'Test' => 1 ], $instance->getArrayCopy(), 'The uksort occurred.');
	}

	/**
	 * @covers ::uksort
	 */
	public function testThatWeCannotUksortAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'a' => 3, 'b' => 1, 'c' => 2 ]);
		$instance->freeze();
		// Now we can't sort.
		$instance->uksort(function ($lhs, $rhs) {
				 return -strcmp($lhs, $rhs);
			 });
	}

	/**
	 * @covers ::exchangeArray
	 */
	public function testThatWeCanExchangeArrayANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'Test' => 1, 'testing' => 2, 'test' => 3 ]);

		// Now we can exchange.
		$new = [ 30, 40, 50 ];
		$instance->exchangeArray($new);
		$this->assertSame($new, $instance->getArrayCopy(), 'The exchange occurred.');
	}

	/**
	 * @covers ::exchangeArray
	 */
	public function testThatWeCannotExchangeArrayAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject([ 'Test' => 1, 'testing' => 2, 'test' => 3 ]);
		$instance->freeze();
		// Now we can't exchange.
		$new = [ 30, 40, 50 ];
		$instance->exchangeArray($new);
	}

	/**
	 * @covers ::getArrayCopy
	 */
	public function testThatWeCanGetArrayCopyOnANotFrozenInstance()
	{
		$input = [ 'Test' => 1, 'testing' => 2, 'test' => 3 ];
		$instance = new FreezableArrayObject($input);
		$this->assertSame($input, $instance->getArrayCopy(), 'We can get the array.');
	}

	/**
	 * @covers ::getArrayCopy
	 */
	public function testThatWeCanGetArrayCopyOnAFrozenInstance()
	{
		$input = [ 'Test' => 1, 'testing' => 2, 'test' => 3 ];
		$instance = new FreezableArrayObject($input);
		$instance->freeze();
		$this->assertSame($input, $instance->getArrayCopy(), 'We can get the array.');
	}

	/**
	 * @covers ::serialize
	 */
	public function testThatWeCanSerializeANotFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 3, 'b' => 2, 'c' => 1 ]);
		$this->assertSame("x:i:0;a:3:{s:1:\"a\";i:3;s:1:\"b\";i:2;s:1:\"c\";i:1;};m:a:1:{s:44:\"\000Kwv\\Peg\\Helpers\\FreezableArrayObject\000frozen\";b:0;}", $instance->serialize(), 'We can serialize the array.');
	}

	/**
	 * @covers ::serialize
	 */
	public function testThatWeCanSerializeAFrozenInstance()
	{
		$instance = new FreezableArrayObject([ 'a' => 3, 'b' => 2, 'c' => 1 ]);
		$instance->freeze();
		$this->assertSame("x:i:0;a:3:{s:1:\"a\";i:3;s:1:\"b\";i:2;s:1:\"c\";i:1;};m:a:1:{s:44:\"\000Kwv\\Peg\\Helpers\\FreezableArrayObject\000frozen\";b:1;}", $instance->serialize(), 'We can serialize the array.');
	}

	/**
	 * @covers ::unserialize
	 */
	public function testThatWeCanUnerializeANotFrozenInstance()
	{
		$instance = new FreezableArrayObject();
		$instance->unserialize("x:i:0;a:3:{s:1:\"a\";i:3;s:1:\"b\";i:2;s:1:\"c\";i:1;};m:a:1:{s:44:\"\000Kwv\\Peg\\Helpers\\FreezableArrayObject\000frozen\";b:0;}");
		$this->assertSame([ 'a' => 3, 'b' => 2, 'c' => 1 ], $instance->getArrayCopy(), 'We successfully unserialized the array.');
	}

	/**
	 * @covers ::unserialize
	 */
	public function testThatWeCannotUnserializeAFrozenInstance()
	{
		$this->expectException(Fault::class);

		$instance = new FreezableArrayObject();
		$instance->freeze();
		// Now we can't unserialize.
		$instance->unserialize("x:i:0;a:3:{s:1:\"a\";i:3;s:1:\"b\";i:2;s:1:\"c\";i:1;};m:a:1:{s:44:\"\000Kwv\\Peg\\Helpers\\FreezableArrayObject\000frozen\";b:1;}");
	}
}