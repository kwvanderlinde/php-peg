<?php
namespace Tests\Kwv\Peg;


class TestCase extends \PHPUnit\Framework\TestCase
{
	/**
	 * Set the expected exception.
	 *
	 * @param string|nul $type
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
}