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

class LngLat implements \ArrayAccess
{
    private $longitude;
    private $latitude;

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
    public function __construct($longitude, $latitude)
    {
        $this->longitude = self::normalizeLng((float) $longitude);
        $this->latitude = self::normalizeLat((float) $latitude);
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
        throw new \BadMethodCallException('LngLat is immutable.');
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('LngLat is immutable.');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%F,%F', $this->getLongitude(), $this->getLatitude());
    }

    /**
     * Returns the approximate sea level great circle (Earth) distance between
     * this point and the destination point using the Haversine formula and
     * assuming an Earth radius of Util::EARTH_RADIUS.
     *
     * @param  mixed            $dest
     * @return \Geokit\Distance The distance to the destination point
     */
    public function distanceTo($dest)
    {
        return Calc::distanceHaversine($this, $dest);
    }

    /**
     * Returns the (initial) heading from this point to the destination point in degrees.
     *
     * @param  mixed $dest
     * @return float Initial heading in degrees from North
     */
    public function headingTo($dest)
    {
        return Calc::heading($this, $dest);
    }

    /**
     * Takes anything which looks like a coordinate and generates a LngLat
     * object from it.
     *
     * $input can be either a string, an array, an \ArrayAccess object or a
     * LngLat object.
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
     * If $input is an LngLat object, it is just passed through.
     *
     * @param  string|array|\ArrayAccess|\Geokit\LngLat $input
     * @return \Geokit\LngLat
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
            $lng = $match[1];
            $lat = $match[2];
        } elseif (is_array($input) || $input instanceof \ArrayAccess) {
            $lat = self::extract($input, self::$latitudeKeys);

            if (!$lat && isset($input[1])) {
                $lat = $input[1];
            }

            $lng = self::extract($input, self::$longitudeKeys);

            if (!$lng && isset($input[0])) {
                $lng = $input[0];
            }
        }

        if (is_numeric($lat) && is_numeric($lng)) {
            return new self($lng, $lat);
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize LngLat from input %s.', json_encode($input)));
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
