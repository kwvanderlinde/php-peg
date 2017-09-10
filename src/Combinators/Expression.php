<?php
namespace Kwv\Peg\Combinators;

interface Expression
{
	function parse(StringView $input): Result;
}