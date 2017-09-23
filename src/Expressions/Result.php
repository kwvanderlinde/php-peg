<?php
namespace Kwv\Peg\Expressions;


/**
 * Represents a successful parse.
 */
class Result
{
	/**
	 * @var int
	 *   The line number of the parse.
	 */
	private $lineNumber;

	/**
	 * @var int
	 *   The column number of the parse.
	 */
	private $columnNumber;

	/**
	 * @var mixed
	 *   The value of the parse.
	 */
	private $value;

	/**
	 * Initialize the result.
	 *
	 * @param int $lineNumber
	 *   The line number of the parse.
	 * @param int $columnNumber
	 *   The column number of the parse.
	 * @param mixed $value
	 *   The value to associate with the parse.
	 */
	public function __construct(int $lineNumber, int $columnNumber, $value)
	{
		$this->lineNumber = $lineNumber;
		$this->columnNumber = $columnNumber;
		$this->value = $value;
	}

	/**
	 * Get the line number of the parse.
	 *
	 * @return int
	 *   The line number of the parse.
	 */
	public function getLineNumber(): int
	{
		return $this->lineNumber;
	}

	/**
	 * Get the column number of the parse.
	 *
	 * @return int
	 *   The column number of the parse.
	 */
	public function getColumnNumber(): int
	{
		return $this->columnNumber;
	}

	/**
	 * Get the value associated with the parse.
	 *
	 * @return mixed
	 *   The value associated with the parse.
	 */
	public function getValue()
	{
		return $this->value;
	}
}