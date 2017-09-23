<?php
namespace Kwv\Peg\Expressions;

use Throwable;
use Base\Exceptions\BaseException;
use Base\Exceptions\Contingency;

use Kwv\Peg\StringView;


/**
 * Thrown when a parse fails.
 */
class ParseFailedException extends BaseException implements Contingency
{
	/**
	 * @var Expression
	 *   The expression which failed to parse.
	 */
	private $expression;

	/**
	 * @var StringView
	 *   The input view on which the expression failed.
	 */
	private $input;

	/**
	 * Initialize the exception.
	 *
	 * @param Expression $expression
	 *   The expression which failed to parse.
	 * @param StringView $input
	 *   The input which the expression failed to parse.
	 * @param Throwable|null $previous
	 *   A failure from a sub-expression that caused this failure, or `null` if the is no previous failure.
	 */
	public function __construct(Expression $expression, StringView $input, Throwable $previous = null)
	{
		parent::__construct('Failed to parse expression', $previous);

		$this->expression = $expression;
		$this->input = $input;
	}

	/**
	 * Get the expression which failed to parse.
	 *
	 * @return Expression
	 *   The expression which failed to parse.
	 */
	public function getExpression(): Expression
	{
		return $this->expression;
	}

	/**
	 * Get the line number in the input at which the failure occurred.
	 *
	 * @return int
	 *   The line number of the failure.
	 */
	public function getLineNumber(): int
	{
		return $this->input->getLineNumber();
	}

	/**
	 * Get the column number in the input at which the failure occurred.
	 *
	 * @return int
	 *   The column number of the failure.
	 */
	public function getColumnNumber(): int
	{
		return $this->input->getColumnNumber();
	}
}