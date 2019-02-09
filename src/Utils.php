<?php

declare(strict_types=1);

namespace Geokit;

abstract class Utils
{
    /**
     * Normalizes a latitude in degrees to the -90,90 range.
     * @see https://github.com/postgis/postgis/blob/153cf2b8e346182e371d0fdec7f34baaf78c4334/liblwgeom/lwgeodetic.c#L133-L155
     */
    public static function normalizeLat(float $lat): float
    {
        if ($lat > 360.0) {
            $lat = \fmod($lat, 360.0);
        }

        if ($lat < -360.0) {
            $lat = \fmod($lat, -360.0);
        }

        if ($lat > 180.0) {
            $lat = 180.0 - $lat;
        }

        if ($lat < -180.0) {
            $lat = -180.0 - $lat;
        }

        if ($lat > 90.0) {
            $lat = 180.0 - $lat;
        }

        if ($lat < -90.0) {
            $lat = -180.0 - $lat;
        }

        return $lat;
    }

    /**
     * Normalizes a longitude in degrees to the -180,180 range.
     * @see https://github.com/postgis/postgis/blob/153cf2b8e346182e371d0fdec7f34baaf78c4334/liblwgeom/lwgeodetic.c#L106-L127
     */
    public static function normalizeLng(float $lng): float
    {
        if ($lng > 360.0) {
            $lng = \fmod($lng, 360.0);
        }

        if ($lng < -360.0) {
            $lng = \fmod($lng, -360.0);
        }

        if ($lng > 180.0) {
            $lng = -360.0 + $lng;
        }

        if ($lng < -180.0) {
            $lng = 360.0 + $lng;
        }

        if ($lng === -180.0) {
            return 180.0;
        }

        if ($lng === -360.0) {
            return 0.0;
        }

        return $lng;
    }
}
