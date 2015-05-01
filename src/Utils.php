<?php

namespace Geokit;

class Utils
{
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
