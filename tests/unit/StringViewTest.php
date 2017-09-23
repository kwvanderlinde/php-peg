<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg;

use stdClass;
use ArgumentCountError;
use TypeError;
use Base\Exceptions\LogicException;
use Kwv\Peg\StringView;
use Kwv\Peg\RegexFailedException;


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
		$argumentCountError = version_compare(\PHP_VERSION, '7.1'. '<') ? TypeError::class : ArgumentCountError::class;

		return [
			'Can be constructed with a string and an integer offset and an integer length and an integer line number and an integer column' => [ [ 'Hello, world', 2, 5, 1, 3 ] ],
			'Cannot be constructed with just a string' => [ [ 'Hello, world' ], [ $argumentCountError ] ],
			'Cannot be constructed with just a string and an offset' => [ [ 'Hello, world', 2 ], [ $argumentCountError ] ],
			'Cannot be constructed with just a string and a null offset' => [ [ 'Hello, world', null ], [ TypeError::class ] ],
			'Cannot be constructed with a string and a null offset and a length' => [ [ 'Hello, world', null, 3 ], [ TypeError::class ] ],
			'Cannot be constructed with a string and an offset and a null length' => [ [ 'Hello, world', 2, null ], [ TypeError::class ] ],
			'Cannot be constructed with a negative offset' => [ [ 'Hello, world', -1, 5, 1, 2 ], [ LogicException::class, 'Offset must not be negative.' ] ],
			'Cannot be constructed with a negative length' => [ [ 'Hello, world', 5, -1, 1, 2 ], [ LogicException::class, 'Length must not be negative.' ] ],
			'Can be constructed with an offset which is exactly than the string length if the length is 0' => [ [ 'Hello, world', 12, 0, 1, 12 ] ],
			'Cannot be constructed with an offset which is greater than the string length' => [ [ 'Hello, world', 13, 0, 1, 3 ], [ LogicException::class, 'View cannot extend past the end of the string.' ] ],
			'Cannot be constructed with an offset and length which extends past the end' => [ [ 'Hello, world', 5, 8, 1, 3 ], [ LogicException::class, 'View cannot extend past the end of the string.' ] ],
			'Can be constructed with an offset and length which extends to just before the end' => [ [ 'Hello, world', 5, 7, 1, 3 ] ],
			'Cannot be constructed with a negative line number' => [ [ 'Hello, world', 5, 7, -1, 3 ], [ LogicException::class, 'Line number must not be negative.' ]  ],
			'Cannot be constructed with a negative column number' => [ [ 'Hello, world', 5, 7, 1, -3 ], [ LogicException::class, 'Column number must not be negative.' ] ],
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

	/**
	 * @param string $string
	 *   The string to wrap in a `StringView`.
	 * @param int $offset
	 *   The offset into the string.
	 * @param int $length
	 *   The length of the view.
	 * @param mixed $index
	 *   The value to test against.
	 * @param string $expected
	 *   The character to expect at the given index.
	 * @param array|null $expectedException
	 *   The expected exception, or `null` if no exception is expected.
	 *
	 * @covers ::at
	 * @dataProvider atProvider
	 */
	public function testAt(string $string, int $offset, int $length, $index, string $expected, array $expectedException = null)
	{
		if (null !== $expectedException)
		{
			$this->expectFullException(...$expectedException);
		}

		$input = new StringView($string, $offset, $length, 0, 0);
		$result = $input->at($index);
		$this->assertSame($expected, $result, 'Found expected character.');
	}

	public function atProvider()
	{
		$testString = 'This is a good day.';

		return [
			[ $testString, 0, 18, 0, 'T' ],
			[ $testString, 5, 6, 0, 'i' ],
			[ $testString, 0, 18, 5, 'i' ],
			[ $testString, 5, 6, 2, ' ' ],
			'Negative index is invalid' => [ $testString, 0, 18, -1, '', [ LogicException::class ] ],
			'Index greater than window length is invalid' => [ $testString, 5, 6, 7, '', [ LogicException::class ] ],
			'Object is not an index' => [ $testString, 5, 6, new \stdClass, '', [ TypeError::class ] ]
		];
	}

	/**
	 * @param string $string
	 *   The string to wrap in a `StringView`.
	 * @param int $offset
	 *   The offset into the string.
	 * @param int $length
	 *   The length of the view.
	 *
	 * @covers ::getLength
	 * @dataProvider getLengthProvider
	 */
	public function testGetLength(string $string, int $offset, int $length)
	{
		$input = new StringView($string, $offset, $length, 0, 0);
		$this->assertSame($length, $input->getLength(), 'View is of the expected length.');
	}

	public function getLengthProvider()
	{
		$testString = 'This is a good day.';

		return [
			[ $testString, 0, 18, 0],
			[ $testString, 5, 6, 0 ],
			[ $testString, 0, 18, 5 ],
			[ $testString, 5, 6, 2 ],
		];
	}

	/**
	 * @param string $string
	 *   The string to wrap in a `StringView`.
	 * @param int $offset
	 *   The offset into the string.
	 * @param int $length
	 *   The length of the view.
	 * @param mixed $regex
	 *   The regex to test.
	 * @param array $expected
	 *   The expected result of the regex match.
	 * @param array|null $expectedException
	 *   The expected exception, or `null` if no exception is expected.
	 *
	 * @covers ::matchRegex
	 * @dataProvider matchRegexProvider
	 */
	public function testMatchRegex(string $string, int $offset, int $length, $regex, array $expected, array $expectedException = null)
	{
		if (null !== $expectedException)
		{
			$this->expectFullException(...$expectedException);
		}

		$input = new StringView($string, $offset, $length, 0, 0);
		$results = $input->matchRegex($regex);
		$this->assertSame($expected, $results, 'The regular expression matched as expected.');
	}

	public function matchRegexProvider()
	{
		$testString = 'This is a good day.';

		return [
			'Object is not a valid regex' => [ $testString, 0, 18, new stdClass, [], [ TypeError::class ] ],
			'Empty string is not a valid regex' => [ $testString, 0, 18, '', [], [ LogicException::class ] ],
			'Empty regex' => [ $testString, 0, 18, '//', [
					0 => ''
				]],
			'Empty match at start of view' => [ 'abcdefghijk', 3, 4, '/(?=d)5*/', [
					0 => ''
				]],
			'Empty match just before start of view' => [ 'abcdefghijk', 3, 4, '/(?=c)5*/', [], [ RegexFailedException::class ]],
			'Empty match at end of view' => [ 'abcdefghijk', 3, 4, '/(?=h)5*/', [
					0 => ''
				]],
			'Empty match just after end of view' => [ 'abcdefghijk', 3, 4, '/(?=i)5*/', [], [ RegexFailedException::class ]],
			'Regex matches within view' => [ $testString, 5, 6, '/\s*a\s*/', [
					0 => ' a '
				]],
			'Regex matches before view' => [ $testString, 5, 6, '/This/', [], [ RegexFailedException::class ]],
			'Regex matches after view' => [ $testString, 5, 6, '/day/', [], [ RegexFailedException::class ] ],
			'Regex match stradles front of view' => [ $testString, 5, 6, '/this is/', [], [ RegexFailedException::class ] ],
			'Regex match stradles end of view' => [ $testString, 5, 6, '/good/', [], [ RegexFailedException::class ] ],
			'Regex match is barely within front of view' => [ $testString, 5, 6, '/is/', [
					0 => 'is'
				]],
			'Regex match is barely within end of view' => [ $testString, 5, 6, '/\s*g/', [
					0 => ' g'
				]],
			'Regex matches before and after view' => [ 'token some other stuff token', 5, 18, '/token/', [], [ RegexFailedException::class ] ],
			'Regex matches before and within view' => [ 'token some token stuff snort', 5, 18, '/token/', [
					0 => 'token'
				]],
			'Regex matches after and within view' => [ 'snort some token stuff token', 5, 18, '/token/', [
					0 => 'token'
				]],
			'Regex matches before, after and within view' => [ 'token some token stuff token', 5, 18, '/token/', [
					0 => 'token'
				]],
		];
	}
}