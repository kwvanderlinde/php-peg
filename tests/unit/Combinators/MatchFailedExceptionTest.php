<?php
namespace Tests\Kwv\Peg\Expressions;

use Error;
use Exception;
use stdClass;
use TypeError;

use Base\Exceptions\Contingency;

use Kwv\Peg\Expressions\Expression;
use Kwv\Peg\Expressions\MatchFailedException;
use Kwv\Peg\StringView;


/**
 * @coversDefaultClass Kwv\Peg\Expressions\MatchFailedException
 */
class MatchFailedExceptionTest extends \Tests\Kwv\Peg\TestCase
{
	/**
	 * @coversNothing
	 */
	public function testIsAnException()
	{
		$this->assertSubtypeOf(Exception::class, MatchFailedException::class);
	}

	/**
	 * @coversNothing
	 */
	public function testIsAnContingency()
	{
		$this->assertSubtypeOf(Contingency::class, MatchFailedException::class);
	}

	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the constructor.
	 * @param array|null $expectedException
	 *   The exception to expect, or `null` if no exception is expected.
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

		$instance = new MatchFailedException(...$args);
		$this->assertInstanceOf(MatchFailedException::class, $instance);
	}

	public function constructProvider()
	{
		$expression = $this->prophesize(Expression::class)->reveal();
		$input = $this->prophesize(StringView::class)->reveal();
		$error = $this->prophesize(Error::class)->reveal();
		$exception = $this->prophesize(Exception::class)->reveal();

		return [
			'Can be constructed with an expression and a string view' => [ [ $expression, $input ] ],
			'Can be constructed with an expression and a string view and null' => [ [ $expression, $input, null ] ],
			'Can be constructed with an expression and a string view and an error' => [ [ $expression, $input, $error ] ],
			'Can be constructed with an expression and a string view and an exception' => [ [ $expression, $input, $exception ] ],
			'Cannot be constructed with a stdClass and a string view' => [ [ new stdClass, $input ], [ TypeError::class ] ],
			'Cannot be constructed with an expression and a stdClass' => [ [ $expression, new stdClass ], [ TypeError::class ] ],
		];
	}

	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the constructor.
	 * @param Expression $expression
	 *   The expected object to be returned as the failed expression.
	 *
	 * @covers ::getExpression
	 * @dataProvider getExpressionProvider
	 */
	public function testGetExpression(array $args, Expression $expression)
	{
		$instance = new MatchFailedException(...$args);
		$this->assertSame($expression, $instance->getExpression());
	}

	public function getExpressionProvider()
	{
		$expression = $this->prophesize(Expression::class);
		$input = $this->prophesize(StringView::class);

		return [
			'Same expression object is returned' => [ [ $expression->reveal(), $input->reveal() ], $expression->reveal() ]
		];
	}

	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the constructor.
	 * @param int $lineNumber
	 *   The line number.
	 *
	 * @covers ::getLineNumber
	 * @dataProvider getLineNumberProvider
	 */
	public function testLineNumber(array $args, int $lineNumber)
	{
		$instance = new MatchFailedException(...$args);
		$this->assertSame($lineNumber, $instance->getLineNumber());
	}

	public function getLineNumberProvider()
	{
		$expression = $this->prophesize(Expression::class);
		$input = $this->prophesize(StringView::class);

		$lineNumbers = [ 1, 12, 43 ];
		foreach ($lineNumbers as $lineNumber)
		{
			$input = $this->prophesize(StringView::class);
			$input->getLineNumber()->willReturn($lineNumber)->shouldBeCalled();
			yield "Line number {$lineNumber} is returned" => [ [ $expression->reveal(), $input->reveal() ], $lineNumber ];
		}
	}

	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the constructor.
	 * @param int $columnNumber
	 *   The column number.
	 *
	 * @covers ::getColumnNumber
	 * @dataProvider getColumnNumberProvider
	 */
	public function testColumnNumber(array $args, int $columnNumber)
	{
		$instance = new MatchFailedException(...$args);
		$this->assertSame($columnNumber, $instance->getColumnNumber());
	}

	public function getColumnNumberProvider()
	{
		$expression = $this->prophesize(Expression::class);

		$columnNumbers = [ 1, 12, 43 ];
		foreach ($columnNumbers as $columnNumber)
		{
			$input = $this->prophesize(StringView::class);
			$input->getColumnNumber()->willReturn($columnNumber)->shouldBeCalled();
			yield "Column number {$columnNumber} is returned" => [ [ $expression->reveal(), $input->reveal() ], $columnNumber ];
		}
	}

	/**
	 * @param mixed[] $args
	 *   The arguments to pass to the constructor.
	 * @param \Throwable|null $previous
	 *   The expected chained exception.
	 *
	 * @covers ::getPrevious
	 * @dataProvider getPreviousProvider
	 */
	public function testGetPrevious(array $args, \Throwable $previous = null)
	{
		$instance = new MatchFailedException(...$args);
		$this->assertSame($previous, $instance->getPrevious());
	}

	public function getPreviousProvider()
	{
		$expression = $this->prophesize(Expression::class);
		$input = $this->prophesize(StringView::class);
		$exception = $this->prophesize(Exception::class);
		$error = $this->prophesize(Error::class);

		$previouses = [
			'Null is returned if no previous throwable is set' => false,
			'Null is returned if previous throwable is explicitly set to null' => null,
			'Previous exception is returned if set' => $exception->reveal(),
			'Previous error is returned if set' => $error->reveal()
		];
		foreach ($previouses as $key => $previous)
		{
			$args = [ $expression->reveal(), $input->reveal() ];

			if (false === $previous)
			{
				// Note that this is not added to the argument list.
				$previous = null;
			}
			else
			{
				$args[] = $previous;
			}

			yield $key => [ $args, $previous ];
		}
	}
}