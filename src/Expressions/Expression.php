<?php
namespace Kwv\Peg\Expressions;

use Kwv\Peg\StringView;


interface Expression
{
	function parse(StringView $input): Result;
}