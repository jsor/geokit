<?php

declare(strict_types=1);

namespace Geokit;

use const M_PI;
use function asin;
use function fmod;
use function sin;

abstract class Utils
{
    /**
     * Normalize a latitude into the range (-90, 90) (upper and lower bound
     * included).
     */
    public static function normalizeLat(float $lat): float
    {
        return asin(sin(($lat / 180) * M_PI)) * (180 / M_PI);
    }

    /**
     * Normalize a longitude into the range (-180, 180) (lower bound excluded,
     * upper bound included).
     */
    public static function normalizeLng(float $lng): float
    {
        $mod = fmod($lng, 360);

        if ($mod <= -180) {
            $mod += 360;
        }

        if ($mod > 180) {
            $mod -= 360;
        }

        return $mod;
    }
}
