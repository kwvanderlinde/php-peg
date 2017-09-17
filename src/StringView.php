<?php
namespace Kwv\Peg;

use InvalidArgumentException;


/**
 * A constrained view of a string.
 *
 * This allows moving a "window" around a string without having to always create new strings.
 */
class StringView
{
	/**
	 * @var string
	 *   The wrapped string.
	 */
	private $string;

	/**
	 * @var int
	 *   The offset of the view into the wrapped string.
	 */
	private $offset;

	/**
	 * @var int
	 *   The length of the view.
	 */
	private $length;

	/**
	 * @var int
	 *   The current line of input.
	 */
	private $lineNumber;

	/**
	 * @var int
	 *   The current column of input.
	 */
	public $columNumber;

	/**
	 * Initialize the string view.
	 *
	 * @param string $string
	 *   The string to wrap.
	 * @param int $offset
	 *   The offset into the string. Nothing before this index will be accessible by this view.
	 * @param int $length
	 *   The length of the view. Nothing past this length will be accessible by this view.
	 * @param int $lineNumber
	 *   The line number of the current position in the input.
	 * @param int $columnNumber
	 *   The column number of the current position in the input.
	 */
	public function __construct(string $string, int $offset, int $length, int $lineNumber, int $columnNumber)
	{
		if ($offset < 0)
		{
			throw new InvalidArgumentException('Offset must not be negative.');
		}
		if ($length < 0)
		{
			throw new InvalidArgumentException('Length must not be negative.');
		}
		if ($offset + $length > \strlen($string))
		{
			throw new InvalidArgumentException('View cannot extend past the end of the string.');
		}
		if ($lineNumber < 0)
		{
			throw new InvalidArgumentException('Line number must not be negative.');
		}
		if ($columnNumber < 0)
		{
			throw new InvalidArgumentException('Column number must not be negative.');
		}

		$this->string = $string;
		$this->offset = $offset;
		$this->length = $length;
		$this->lineNumber = $lineNumber;
		$this->columnNumber = $columnNumber;
	}

	public function getLineNumber(): int
	{
		return $this->lineNumber;
	}

	public function getColumnNumber(): int
	{
		return $this->columnNumber;
	}

	public function startsWith(string $prefix): bool
	{
		if (strlen($prefix) > $this->length)
		{
			// No way we can match a string larger than our viewing window!
			return false;
		}

		// Now we just need to see if the string exists at our current offset.
		return 0 === strpos($this->string, $prefix, $this->offset);
	}
}