<?php
namespace Tests\Kwv\Peg;

use \PHPUnit\Framework\TestCase as BaseTestCase;
use \PHPUnit\Framework\Constraint\Constraint;
use \PHPUnit\Framework\Constraint\LogicalAnd;


class TestCase extends BaseTestCase
{
	/**
	 * Set the expected exception.
	 *
	 * @param string|null $type
	 *   The expected exception type, or `null` if no specific exception type is expected.
	 * @param string|null $message
	 *   The message to expect, or `null` if no specific message is expected. The interpretation of this parameter depends
	 *   on `$messageIsRegex`.
	 * @param bool $messageIsRegex
	 *   If `true`, the expected message will be treated as a regular expression for matching the exception message.
	 * @return void
	 */
	protected function expectFullException(string $type = null, string $message = null, bool $messageIsRegex = false)
	{
		if (null === $type)
		{
			$type = \Throwable::class;
		}
		$this->expectException($type);

		if (null !== $message)
		{
			if ($messageIsRegex)
			{
				$this->expectExceptionMessageRegExp($message);
			}
			else
			{
				$this->expectExceptionMessage($message);
			}
		}
	}

	protected function assertSubtypeOf(string $expected, $type, string $message = '')
	{
		$assertion = new LogicalAnd();
		$assertion->setConstraints([ new TypeExists(), new IsSubtype($expected) ]);

		static::assertThat($type, $assertion, $message);
	}
}

class TypeExists extends Constraint
{
	public function matches($type)
	{
		if (!is_string($type))
		{
			return false;
		}

		return class_exists($type) || interface_exists($type);
	}

	public function toString()
	{
		return "is a type name";
	}
}

class IsSubtype extends Constraint
{
	/**
	 * @var string
	 *   The expected class or interface.
	 */
	private $expected;

	public function __construct(string $expected)
	{
		parent::__construct();

		$this->expected = $expected;
	}

	public function matches($type)
	{
		return is_string($type) && is_a($type, $this->expected, true);
	}

	public function toString()
	{
		return "names a subtype of {$this->expected}";
	}
}