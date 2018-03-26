<?php

namespace Reinforcement\Support;

use Illuminate\Support\Str as IlluminateStr;

class Str extends IlluminateStr
{
    public static function indent(int $level = 1, string $string = '', int $spaces = 4) {
        return str_repeat(" ", ($level * $spaces)).$string;
    }
}