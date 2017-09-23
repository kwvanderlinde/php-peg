<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg\Expressions;

use Kwv\Peg\Expressions\Expression;
use Kwv\Peg\Expressions\Literal;
use Kwv\Peg\Expressions\ParseFailedException;
use Kwv\Peg\StringView;


/**
 * @coversDefaultClass Kwv\Peg\Expressions\Literal
 */
class LiteralTest extends \Tests\Kwv\Peg\TestCase
{
	public function testIsExpression()
	{
		$this->assertSubtypeOf(Expression::class, Literal::class);
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

		$instance = new Literal(...$args);
		$this->assertInstanceOf(Literal::class, $instance);
	}

	public function constructProvider()
	{
		// Argument count error wasn't added until PHP 7.1.
		$argumentCountError = \version_compare(\PHP_VERSION, '7.1'. '<') ? \TypeError::class : \ArgumentCountError::class;

		return [
			'Can be constructed with a string' => [ [ 'a literal' ] ],
			'Cannot be constructed with an int' => [ [ 0 ], [ \TypeError::class ] ],
			'Cannot be constructed without arguments' => [ [ ], [ $argumentCountError ] ],
			'Cannot be constructed with null' => [ [ null ], [ \TypeError::class ] ],
		];
	}

	/**
	 * @param string $literal
	 *   The literal string to parse.
	 * @param StringView $input
	 *   The input view against which the literal will be parsed.
	 * @param int $expectedLineNumber
	 *   The expected line number of the parse.
	 * @param int $expectedColumnNumber
	 *   The expected column number of the parse.
	 * @param mixed $expectedValue
	 *   The expected value of the parse.
	 * @param array|null $expectedException
	 *   The expected exception, or `null` if no exception is expected.
	 *
	 * @covers ::parse
	 * @dataProvider parseProvider
	 */
	public function testParse(string $literal, StringView $input, int $expectedLineNumber, int $expectedColumnNumber, $expectedValue, array $expectedException = null)
	{
		if (null !== $expectedException)
		{
			$this->expectFullException(...$expectedException);
		}

		$literal = new Literal($literal);

		$result = $literal->parse($input);

		$this->assertSame($expectedLineNumber, $result->getLineNumber(), 'The parse occurred on the correct line.');
		$this->assertSame($expectedColumnNumber, $result->getColumnNumber(), 'The parse occurred in the correct column.');
		$this->assertSame($expectedValue, $result->getValue(), 'The parse produced the correct value.');
	}

	public function parseProvider()
	{
		return [
			'Literal parses start of input view' => [ 'this', new StringView('this is a string', 0, 4, 1, 2), 1, 2, 'this' ],
			'Literal does not parse if case is wrong' => [ 'This', new StringView('this is a string', 0, 4, 1, 2), 1, 2, '', [ ParseFailedException::class ] ],
			'Literal parse fails with ParseFailedException' => [ 'not this', new StringView('this is a string', 0, 4, 1, 2), 1, 2, '', [ ParseFailedException::class ] ],
		];
	}
}