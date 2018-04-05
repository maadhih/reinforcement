<?php

namespace Reinforcement\Support;

use Illuminate\Support\Str as IlluminateStr;
use Stringy\Stringy;

class Str extends IlluminateStr
{
    public static function indent(int $level = 1, string $string = '', int $spaces = 4) {
        return str_repeat(" ", ($level * $spaces)).$string;
    }

    public static function insertAfter($string, $insert, $after, $last = false)
    {
        $string = Stringy::create($string);
        $index = $last ? $string->indexOfLast($after) : $string->indexOf($after);
        $firstHalf = $string->substr(0, $index+strlen($after));
        $secondHalf = $string->substr($index+strlen($after));

        return $firstHalf . $insert . $secondHalf;
    }

    public static function insertAfterLast($string, $insert, $after)
    {
        return static::insertAfter($string, $insert, $after, true);
    }
}