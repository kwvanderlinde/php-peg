<?php
namespace Kwv\Peg;

use Base\Exceptions\{BaseException, Contingency};


class RegexFailedException extends BaseException implements Contingency
{
	/**
	 * @var StringView
	 *   The input on which the regular expression failed to match.
	 */
	private $stringView;

	/**
	 * @var string
	 *   The regular expression which failed to match.
	 */
	private $regex;

	public function __construct(StringView $stringView, string $regex)
	{
		parent::__construct('Unable to match regular expression.');

		$this->stringView = $stringView;
		$this->regex = $regex;
	}
}