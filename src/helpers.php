<?php

if (! function_exists('is_array_assoc')) {
    function is_array_assoc(array $array)
    {
        if (array() === $array) return false;
	    return array_keys($array) !== range(0, count($array) - 1);
    }
}