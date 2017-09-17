<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg;

use Kwv\Peg\StringView;


/**
 * @coversDefaultClass Kwv\Peg\StringView
 */
class StringViewTest extends \Tests\Kwv\Peg\TestCase
{
	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the `StringView` constructor.
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

		$stringView = new StringView(...$args);
		$this->assertInstanceOf(StringView::class, $stringView);
	}

	public function constructProvider()
	{
		// Argument count error wasn't added until PHP 7.1.
		$argumentCountError = \version_compare(\PHP_VERSION, '7.1'. '<') ? \TypeError::class : \ArgumentCountError::class;

		return [
			'Can be constructed with a string and an integer offset and an integer length and an integer line number and an integer column' => [ [ 'Hello, world', 2, 5, 1, 3 ] ],
			'Cannot be constructed with just a string' => [ [ 'Hello, world' ], [ $argumentCountError ] ],
			'Cannot be constructed with just a string and an offset' => [ [ 'Hello, world', 2 ], [ $argumentCountError ] ],
			'Cannot be constructed with just a string and a null offset' => [ [ 'Hello, world', null ], [ \TypeError::class ] ],
			'Cannot be constructed with a string and a null offset and a length' => [ [ 'Hello, world', null, 3 ], [ \TypeError::class ] ],
			'Cannot be constructed with a string and an offset and a null length' => [ [ 'Hello, world', 2, null ], [ \TypeError::class ] ],
			'Cannot be constructed with a negative offset' => [ [ 'Hello, world', -1, 5, 1, 2 ], [ \InvalidArgumentException::class, 'Offset must not be negative.' ] ],
			'Cannot be constructed with a negative length' => [ [ 'Hello, world', 5, -1, 1, 2 ], [ \InvalidArgumentException::class, 'Length must not be negative.' ] ],
			'Can be constructed with an offset which is exactly than the string length if the length is 0' => [ [ 'Hello, world', 12, 0, 1, 12 ] ],
			'Cannot be constructed with an offset which is greater than the string length' => [ [ 'Hello, world', 13, 0, 1, 3 ], [ \InvalidArgumentException::class, 'View cannot extend past the end of the string.' ] ],
			'Cannot be constructed with an offset and length which extends past the end' => [ [ 'Hello, world', 5, 8, 1, 3 ], [ \InvalidArgumentException::class, 'View cannot extend past the end of the string.' ] ],
			'Can be constructed with an offset and length which extends to just before the end' => [ [ 'Hello, world', 5, 7, 1, 3 ] ],
			'Cannot be constructed with a negative line number' => [ [ 'Hello, world', 5, 7, -1, 3 ], [ \InvalidArgumentException::class, 'Line number must not be negative.' ]  ],
			'Cannot be constructed with a negative column number' => [ [ 'Hello, world', 5, 7, 1, -3 ], [ \InvalidArgumentException::class, 'Column number must not be negative.' ] ],
		];
	}

	/**
	 * @param string $string
	 *   The string to wrap in a `StringView`.
	 * @param int $offset
	 *   The offset into the string.
	 * @param int $length
	 *   The length of the view.
	 * @param int $lineNumber
	 *   The line number to associate with the start of the view.
	 * @param int $columnNumber
	 *   The column number to associate with the start of the view.
	 *
	 * @covers ::getLineNumber
	 * @dataProvider propertyProvider
	 */
	public function testGetLineNumber(string $string, int $offset, int $length, int $lineNumber, int $columnNumber)
	{
		$stringView = new StringView($string, $offset, $length, $lineNumber, $columnNumber);

		$this->assertSame($lineNumber, $stringView->getLineNumber(), 'The line number is the one passed into the constructor.');
	}

	/**
	 * @param string $string
	 *   The string to wrap in a `StringView`.
	 * @param int $offset
	 *   The offset into the string.
	 * @param int $length
	 *   The length of the view.
	 * @param int $lineNumber
	 *   The line number to associate with the start of the view.
	 * @param int $columnNumber
	 *   The column number to associate with the start of the view.
	 *
	 * @covers ::getColumnNumber
	 * @dataProvider propertyProvider
	 */
	public function testGetColumnNumber(string $string, int $offset, int $length, int $lineNumber, int $columnNumber)
	{
		$stringView = new StringView($string, $offset, $length, $lineNumber, $columnNumber);

		$this->assertSame($columnNumber, $stringView->getColumnNumber(), 'The line number is the one passed into the constructor.');
	}

	public function propertyProvider()
	{
		return [
			'' => [ 'something', 1, 3, 0, 2 ]
		];
	}

	public function testThatLineNumberDoesNotNeedToCorrespondToInput()
	{
		$stringView = new StringView("This\nis\na\nmultiline\input", 6, 5, 0, 0);
		$expectedLineNumber = 1;
		$this->assertNotSame($expectedLineNumber, $stringView->getLineNumber(), 'The line number does not correspond to the true line number.');
	}

	public function testThatColumnNumberDoesNotNeedToCorrespondToInput()
	{
		$stringView = new StringView("This\nis\na\nmultiline\input", 6, 5, 0, 0);
		$expectedColumnNumber = 1;
		$this->assertNotSame($expectedColumnNumber, $stringView->getColumnNumber(), 'The line number does not correspond to the true line number.');
	}

	/**
	 * @param string $string
	 *   The string to wrap in a `StringView`.
	 * @param int $offset
	 *   The offset into the string.
	 * @param int $length
	 *   The length of the view.
	 * @param mixed $test
	 *   The value to test against.
	 * @param bool $expected
	 *   Whether `$test` is expected to be found at the start of the `StringView`.
	 * @param array|null $expectedException
	 *   The expected exception, or `null` if no exception is expected.
	 *
	 * @covers ::startsWith
	 * @dataProvider startsWithProvider
	 */
	public function testStartsWith(string $string, int $offset, int $length, $test, bool $expected, array $expectedException = null)
	{
		if (null !== $expectedException)
		{
			$this->expectFullException(...$expectedException);
		}

		$input = new StringView($string, $offset, $length, 0, 0);
		$result = $input->startsWith($test);
		$this->assertSame($expected, $result, 'Input ' . ($expected ? 'starts' : 'does not start') . " with '{$test}'.");
	}

	public function startsWithProvider()
	{
		$testString = 'This is a good day';

		return [
			[ $testString, 0, 18, 'This', true ],
			[ $testString, 0, 18, 'That', false ],
			[ $testString, 0, 18, 'this', false ],
			[ $testString, 0, 18, 'good', false ],
			[ $testString, 0, 4, 'This', true ],
			[ $testString, 0, 3, 'This', false ],
			'Cannot call startsWith with an object' => [ $testString, 0, 3, new \stdClass, false, [ \TypeError::class ] ],
		];
	}
}