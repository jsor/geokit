<?php

namespace Geokit;

class Utils
{
    /**
     * Normalizes a latitude to the (-90, 90) range. Latitudes above 90 or
     * below -90 are capped, not wrapped.
     *
     * @param  float $lat The latitude to normalize, in degrees
     * @return float The latitude, fit within the (-90, 90) range
     */
    public static function normalizeLat($lat)
    {
        return max(-90, min(90, $lat));
    }

    /**
     * Normalizes a longitude to the (-180, 180) range. Longitudes above 180
     * or below -180 are wrapped.
     *
     * @param  float $lng The longitude to normalize, in degrees
     * @return float The longitude, fit within the (-180, 180) range
     */
    public static function normalizeLng($lng)
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

    public static function castToBounds($input)
    {
        try {
            return Bounds::normalize($input);
        } catch (\InvalidArgumentException $e) {
        }

        try {
            $latLng = LatLng::normalize($input);

            return new Bounds($latLng, $latLng);
        } catch (\InvalidArgumentException $e) {
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Cannot cast to Bounds from input %s.',
                json_encode($input)
            )
        );
    }

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
