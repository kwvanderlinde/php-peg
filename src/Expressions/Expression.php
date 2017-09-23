<?php
namespace Kwv\Peg\Expressions;

use Kwv\Peg\StringView;


/**
 * The basic interface for all expressions.
 *
 * Expressions are really simple. All they need to be able to do is parse some input.
 */
interface Expression
{
	/**
	 * Attempt to parse some input.
	 *
	 * @param StringView $input
	 *   The input to parse.
	 *
	 * @return Result
	 *   If the parse is successful, the result object describing the parse.
	 *
	 * @throws ParseFailedException
	 *   If the parse is not succesful.
	 */
	function parse(StringView $input): Result;
}