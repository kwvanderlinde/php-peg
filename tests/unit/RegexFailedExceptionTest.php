<?php
declare(strict_types=1);

namespace Tests\Kwv\Peg;

use Exception;
use TypeError;
use Base\Exceptions\Contingency;
use Kwv\Peg\{RegexFailedException, StringView};


/**
 * @coversDefaultClass Kwv\Peg\RegexFailedException
 */
class RegexFailedExceptionTest extends \Tests\Kwv\Peg\TestCase
{
	/**
	 * @coversNothing
	 */
	public function testIsAnException()
	{
		$this->assertSubtypeOf(Exception::class, RegexFailedException::class);
	}

	/**
	 * @coversNothing
	 */
	public function testIsAContingency()
	{
		$this->assertSubtypeOf(Contingency::class, RegexFailedException::class);
	}

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

		$class = RegexFailedException::class;
		$instance = new $class(...$args);
		$this->assertInstanceOf($class, $instance);
	}

	public function constructProvider()
	{
		// Argument count error wasn't added until PHP 7.1.
		$argumentCountError = \version_compare(\PHP_VERSION, '7.1'. '<') ? \TypeError::class : \ArgumentCountError::class;
		$stringView = $this->prophesize(StringView::class);

		return [
			'Can be construct with a StringView and a string' => [ [ $stringView->reveal(), '/a regex/' ] ],
			'Cannot be constructed with only a StringView' => [ [ $stringView->reveal() ], [ $argumentCountError ] ],
			'Cannot be constructed with only a string' => [ [ 'string' ], [ \TypeError::class ] ],
			'Cannot be constructed with only a StringView and an int' => [ [ $stringView->reveal(), 5 ], [ \TypeError::class ] ],
		];
	}
}