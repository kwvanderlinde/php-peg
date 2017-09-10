<?php
namespace Kwv\Peg\Combinators;


/**
 * Represents a successful parse.
 */
class Result
{
	/**
	 * @var int
	 *   The line number of the match.
	 */
	private $lineNumber;

	/**
	 * @var int
	 *   The column number of the match.
	 */
	private $columnNumber;

	/**
	 * @var mixed
	 *   The value of the match.
	 */
	private $value;

	/**
	 * @param int $lineNumber
	 *   The line number of the match.
	 * @param int $columnNumber
	 *   The column number of the match.
	 * @param mixed $value
	 *   The value to associate with the parse.
	 */
	public function __construct(int $lineNumber, int $columnNumber, $value)
	{
		$this->lineNumber = $lineNumber;
		$this->columnNumber = $columnNumber;
		$this->value = $value;
	}

	public function getLineNumber(): int
	{
		return $this->lineNumber;
	}

	public function getColumnNumber(): int
	{
		return $this->columnNumber;
	}

	public function getValue()
	{
		return $this->value;
	}
}