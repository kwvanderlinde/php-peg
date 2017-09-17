<?php
namespace Kwv\Peg\Expressions;

use Base\Exceptions\BaseException;
use Base\Exceptions\Contingency;

use Kwv\Peg\StringView;


class MatchFailedException extends BaseException implements Contingency
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

	public function __construct(Expression $expression, StringView $input, \Throwable $previous = null)
	{
		parent::__construct('Failed to parse expression', $previous);

		$this->expression = $expression;
		$this->input = $input;
	}

	public function getExpression(): Expression
	{
		return $this->expression;
	}

	public function getLineNumber(): int
	{
		return $this->input->getLineNumber();
	}

	public function getColumnNumber(): int
	{
		return $this->input->getColumnNumber();
	}
}