<?php

declare(strict_types=1);

namespace Geokit;

final class LatLng
{
    private $latitude;
    private $longitude;

    private static $longitudeKeys = [
        'longitude',
        'lng',
        'lon',
        'x'
    ];

    private static $latitudeKeys = [
        'latitude',
        'lat',
        'y'
    ];

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = Utils::normalizeLat($latitude);
        $this->longitude = Utils::normalizeLng($longitude);
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function __toString(): string
    {
        return sprintf('%F,%F', $this->getLatitude(), $this->getLongitude());
    }

    /**
     * Takes anything which looks like a coordinate and generates a LatLng
     * object from it.
     *
     * $input can be either a string, an object with getLatitude/getLongitude
     * getter methods, an array, an \ArrayAccess object or a LatLng object.
     *
     * If $input is a string, it can be in the format "1.1234, 2.5678" or
     * "1.1234 2.5678".
     *
     * If $input is an array or \ArrayAccess object, it must have a latitude
     * and longitude entry.
     *
     * Recognized keys are:
     *
     *  * Longitude:
     *    * longitude
     *    * lng
     *    * lon
     *    * x
     *
     *  * Latitude:
     *    * latitude
     *    * lat
     *    * y
     *
     * If $input is an indexed array, it assumes the latitude at index 0
     * and the longitude at index 1, eg. [90.0, 180.0].
     *
     * If $input is an LatLng object, it is just passed through.
     *
     * @param mixed $input
     */
    public static function normalize($input): self
    {
        if ($input instanceof self) {
            return $input;
        }

        $lat = null;
        $lng = null;

        if (is_string($input) && preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $input, $match)) {
            [, $lat, $lng] = $match;
        } elseif (is_object($input) && method_exists($input, 'getLatitude') && method_exists($input, 'getLongitude')) {
            $lat = $input->getLatitude();
            $lng = $input->getLongitude();
        } elseif (is_array($input) || $input instanceof \ArrayAccess) {
            if (Utils::isNumericInputArray($input)) {
                [$lat, $lng] = $input;
            } else {
                $lat = Utils::extractFromInput($input, self::$latitudeKeys);
                $lng = Utils::extractFromInput($input, self::$longitudeKeys);
            }
        }

        if (is_numeric($lat) && is_numeric($lng)) {
            return new self((float) $lat, (float) $lng);
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize LatLng from input %s.', json_encode($input)));
    }
}
