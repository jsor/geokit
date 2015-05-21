<?php

namespace Geokit;

class Utils
{
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
