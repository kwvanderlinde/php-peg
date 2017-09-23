<?php
namespace Kwv\Peg\Expressions;

use Base\Exceptions\LogicException;
use Kwv\Peg\RegexFailedException;
use Kwv\Peg\StringView;


/**
 * Represents a regex-based PEG expression.
 *
 * This is not present in the original PEG specification and is not required.
 * It is useful, however, since it encapsulate the concept of a character class
 * and provides a convenient way to define lexical tokens.
 *
 * Note: this is meant to match a string starting at a specific point. Anchors
 * will not work as expected, and the start of the match will be enforced to be
 * the start of input view.
 */
class Regex implements Expression
{
	/**
	 * @var string
	 *   The regular expression defining the behaviour.
	 */
	private $regex;

	/**
	 * @param string $regex
	 *   The regular expression define this expression's behaviour.
	 */
	public function __construct(string $regex)
	{
		if (false === @preg_match($regex, ''))
		{
			throw new LogicException('Invalid regular expression.');
		}

		$this->regex = $regex;
	}

	/**
	 * @inheritDoc
	 */
	public function parse(StringView $input): Result
	{
		if (0 === $input->getLength())
		{
			throw new ParseFailedException($this, $input);
		}

		try
		{
			$match = $input->matchRegex($this->regex);
		}
		catch (RegexFailedException $ex)
		{
			throw new ParseFailedException($this, $input, $ex);
		}

		return new Result($input->getLineNumber(), $input->getColumnNumber(), $match[0]);
	}
}