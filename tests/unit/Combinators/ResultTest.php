<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg\Expressions\Helpers;

use Kwv\Peg\Expressions\Result;


/**
 * @coversDefaultClass Kwv\Peg\Expressions\Result
 */
class ResultTest extends \Tests\Kwv\Peg\TestCase
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

		$instance = new Result(...$args);
		$this->assertInstanceOf(Result::class, $instance);
	}

	public function constructProvider()
	{
		$items = [
			'Can be constructed with a line number, column number and value' => [ [ 1, 3, 'a value' ] ],
			'Can be constructed with a null value' => [ [ 1, 3, null ] ],
			'Cannot be constructed with a null line number' => [ [ null, 3, 'a value' ], [ \TypeError::class ] ],
			'Cannot be constructed with a null column number' => [ [ 3, null, 'a value' ], [ \TypeError::class ] ],
		];

		// In PHP 7.0, not passing a value for an untyped parameter does not throw an \Error, but generates a error. So this
		// negative test does not working on PHP 7.0.
		if (\version_compare(\PHP_VERSION, '7.1', '>='))
		{
			$items['Cannot be constructed with two arguments'] = [ [ 1, 2 ], [ \ArgumentCountError::class ] ];
		}

		return $items;
	}

	/**
	 * @param int $lineNumber
	 *   The line number of the result.
	 * @param int $columnNumber
	 *   The column number of the result.
	 * @param mixed $value
	 *   The value of the result.
	 *
	 * @covers ::getLineNumber
	 * @dataProvider propertyProvider
	 */
	public function testGetLineNumber(int $lineNumber, int $columnNumber, $value)
	{
		$result = new Result($lineNumber, $columnNumber, $value);

		$this->assertSame($lineNumber, $result->getLineNumber(), 'The returned line number is the one passed to the constructor.');
	}

	/**
	 * @param int $lineNumber
	 *   The line number of the result.
	 * @param int $columnNumber
	 *   The column number of the result.
	 * @param mixed $value
	 *   The value of the result.
	 *
	 * @covers ::getColumnNumber
	 * @dataProvider propertyProvider
	 */
	public function testGetColumnNumber(int $lineNumber, int $columnNumber, $value)
	{
		$result = new Result($lineNumber, $columnNumber, $value);

		$this->assertSame($columnNumber, $result->getColumnNumber(), 'The returned column number is the one passed to the constructor.');
	}

	/**
	 * @param int $lineNumber
	 *   The line number of the result.
	 * @param int $columnNumber
	 *   The column number of the result.
	 * @param mixed $value
	 *   The value of the result.
	 *
	 * @covers ::getValue
	 * @dataProvider propertyProvider
	 */
	public function testGetValue(int $lineNumber, int $columnNumber, $value)
	{
		$result = new Result($lineNumber, $columnNumber, $value);

		$this->assertSame($value, $result->getValue(), 'The returned value is the one passed to the constructor.');
	}

	public function propertyProvider()
	{
		return [
			[ 1, 3, 'a value' ],
			[ 2, 4, 'some other value' ],
		];
	}
}