<?php
namespace Kwv\Peg;

use Base\Exceptions\LogicException;


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
	private $columnNumber;

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
			throw new LogicException('Offset must not be negative.');
		}
		if ($length < 0)
		{
			throw new LogicException('Length must not be negative.');
		}
		if ($offset + $length > \strlen($string))
		{
			throw new LogicException('View cannot extend past the end of the string.');
		}
		if ($lineNumber < 0)
		{
			throw new LogicException('Line number must not be negative.');
		}
		if ($columnNumber < 0)
		{
			throw new LogicException('Column number must not be negative.');
		}

		$this->string = $string;
		$this->offset = $offset;
		$this->length = $length;
		$this->lineNumber = $lineNumber;
		$this->columnNumber = $columnNumber;
	}

	/**
	 * Get the line number of the start of this view.
	 *
	 * @return int
	 *   The line number of the start of this view.
	 */
	public function getLineNumber(): int
	{
		return $this->lineNumber;
	}

	/**
	 * Get the column number of the start of this view.
	 *
	 * @return int
	 *   The column number of the start of this view.
	 */
	public function getColumnNumber(): int
	{
		return $this->columnNumber;
	}

	/**
	 * Test whether this view begins with a string.
	 *
	 * @param string $prefix
	 *   The string to look for at the start of this view.
	 *
	 * @return bool
	 *   `true` if and only if the string was found at the start of this view.
	 */
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

	/**
	 * Get the character at the given index in the view.
	 *
	 * @param int $index
	 *   The index to look up. Must not be negative or past the end of the view.
	 * @return string
	 *   The character found at $index.
	 */
	public function at(int $index): string
	{
		if ($index < 0 || $index >= $this->length)
		{
			throw new LogicException('Index is out of bounds.');
		}

		return $this->string[$this->offset + $index];
	}

	/**
	 * Get the number of characters within this view.
	 *
	 * @return int
	 *   The number of characters within this view.
	 */
	public function getLength(): int
	{
		return $this->length;
	}

	/**
	 * Attempt to match a regular expression against the view.
	 *
	 * The match can succeed only if the entire match lies within this view.
	 *
	 * @param string $regex
	 *   The regular expression to apply.
	 *
	 * @return array
	 *   If the match is successful, the matches as if calling `preg_match` without any flags.
	 *
	 * @throws RegexFailedException
	 *   If the regular expression failed to match the view.
	 */
	public function matchRegex(string $regex)
	{
		$result = @preg_match($regex, $this->string, $matches, \PREG_OFFSET_CAPTURE, $this->offset);

		if (false === $result)
		{
			throw new LogicException('Invalid regular expression.');
		}
		if (0 === $result)
		{
			throw new RegexFailedException($this, $regex);
		}

		// Get the entire match variables.
		list($wholeMatch, $wholeOffset) = $matches[0];
		if ($wholeOffset < $this->offset || $wholeOffset + strlen($wholeMatch) > $this->offset + $this->length)
		{
			throw new RegexFailedException($this, $regex);
		}


		// Success!
		$return = [];
		foreach ($matches as $key => list($match, $offset))
		{
			$return[$key] = $match;
		}
		return $return;
	}
}