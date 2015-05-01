<?php

namespace Geokit;

class Utils
{
    public static function isNumericInputArray($input)
    {
        return isset($input[0]) && isset($input[1]);
    }

    public static function extractFromInput($input, array $keys)
    {
        foreach ($keys as $key) {
            if (!isset($input[$key])) {
                continue;
            }

            return $input[$key];
        }

        return null;
    }
}
