<?php

declare(strict_types=1);

namespace Geokit;

abstract class Utils
{
    /**
     * Normalizes a latitude in degrees to the (-90, 90) range.
     * Latitudes above 90 or below -90 are capped, not wrapped.
     */
    public static function normalizeLat(float $lat): float
    {
        return max(-90, min(90, $lat));
    }

    /**
     * Normalizes a longitude in degrees to the (-180, 180) range.
     * Longitudes above 180 or below -180 are wrapped.
     */
    public static function normalizeLng(float $lng): float
    {
        $mod = fmod($lng, 360);

        if ($mod < -180) {
            return $mod + 360;
        }

        if ($mod > 180) {
            return $mod - 360;
        }

        return $mod;
    }

    public static function isNumericInputArray($input): bool
    {
        return isset($input[0], $input[1]);
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
