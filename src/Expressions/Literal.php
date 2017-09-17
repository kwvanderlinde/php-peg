<?php
namespace Kwv\Peg\Expressions;

use Kwv\Peg\StringView;


/**
 * Represents a literal string PEG expression.
 */
class Literal implements Expression
{
	/**
	 * @var string
	 *   The literal string to match.
	 */
	private $literal;

	/**
	 * @param string $literal
	 *   The literal string to match.
	 */
	public function __construct(string $literal)
	{
		$this->literal = $literal;
	}

	public function parse(StringView $input): Result
	{
		if (!$input->startsWith($this->literal))
		{
			throw new MatchFailedException($this, $input);
		}

		return new Result($input->getLineNumber(), $input->getColumnNumber(), $this->literal);
	}
}