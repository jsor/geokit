<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit;

class LatLng implements \ArrayAccess
{
    private $latitude;
    private $longitude;

    private static $longitudeKeys = array(
        'longitude',
        'lng',
        'lon',
        'x'
    );

    private static $latitudeKeys = array(
        'latitude',
        'lat',
        'y'
    );

    /**
     * @param float $longitude
     * @param float $latitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = self::normalizeLat((float) $latitude);
        $this->longitude = self::normalizeLng((float) $longitude);
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    public function offsetExists($offset)
    {
        return in_array($offset, self::$latitudeKeys) ||
               in_array($offset, self::$longitudeKeys);
    }

    public function offsetGet($offset)
    {
        if (in_array($offset, self::$latitudeKeys)) {
            return $this->getLatitude();
        }

        if (in_array($offset, self::$longitudeKeys)) {
            return $this->getLongitude();
        }

        throw new \InvalidArgumentException(sprintf('Invalid offset %s.', json_encode($offset)));
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('LatLng is immutable.');
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('LatLng is immutable.');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%F,%F', $this->getLatitude(), $this->getLongitude());
    }

    /**
     * Takes anything which looks like a coordinate and generates a LatLng
     * object from it.
     *
     * $input can be either a string, an array, an \ArrayAccess object or a
     * LatLng object.
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
     * If $input is an indexed array, it assumes the longitude at index 0
     * and the latitude at index 1, eg. [180.0, 90.0].
     *
     * If $input is an LatLng object, it is just passed through.
     *
     * @param  string|array|\ArrayAccess|\Geokit\LatLng $input
     * @return \Geokit\LatLng
     * @throws \InvalidArgumentException
     */
    public static function normalize($input)
    {
        if ($input instanceof self) {
            return $input;
        }

        $lat = null;
        $lng = null;

        if (is_string($input) && preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $input, $match)) {
            $lat = $match[1];
            $lng = $match[2];
        } elseif (is_array($input) || $input instanceof \ArrayAccess) {
            $lat = self::extract($input, self::$latitudeKeys);

            if (!$lat && isset($input[0])) {
                $lat = $input[0];
            }

            $lng = self::extract($input, self::$longitudeKeys);

            if (!$lng && isset($input[1])) {
                $lng = $input[1];
            }
        }

        if (is_numeric($lat) && is_numeric($lng)) {
            return new self($lat, $lng);
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize LatLng from input %s.', json_encode($input)));
    }

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

        if ($mod === 180) {
            return 180;
        }

        return $mod < -180 ? $mod + 360 : ($mod > 180 ? $mod - 360 : $mod);
    }

    private static function extract($input, $keys)
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
