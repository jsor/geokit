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

    private static $latitudeKeys = array(
        'latitude',
        'lat',
        'y'
    );

    private static $longitudeKeys = array(
        'longitude',
        'lng',
        'lon',
        'x'
    );

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude = Calc::normalizeLat((float) $latitude);
        $this->longitude = Calc::normalizeLng((float) $longitude);
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
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
     * Returns the approximate sea level great circle (Earth) distance between
     * this point and the destination point using the Haversine formula and
     * assuming an Earth radius of Util::EARTH_RADIUS.
     *
     * @param  \Geokit\LatLng   $dest
     * @return \Geokit\Distance The distance to the destination point
     */
    public function distanceTo(LatLng $dest)
    {
        return Calc::distanceHaversine(
            $this->getLatitude(),
            $this->getLongitude(),
            $dest->getLatitude(),
            $dest->getLongitude()
        );
    }

    /**
     * Returns the (initial) heading from this point to the destination point in degrees.
     *
     * @param  \Geokit\LatLng $dest
     * @return float          Initial heading in degrees from North
     */
    public function headingTo(LatLng $dest)
    {
        return Calc::heading(
            $this->getLatitude(),
            $this->getLongitude(),
            $dest->getLatitude(),
            $dest->getLongitude()
        );
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
     *  * Latitude:
     *    * latitude
     *    * lat
     *    * y
     *
     *  * Longitude:
     *    * longitude
     *    * lng
     *    * lon
     *    * x
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

            if (!$lat && isset($input[1])) {
                $lat = $input[1];
            }

            $lng = self::extract($input, self::$longitudeKeys);

            if (!$lng && isset($input[0])) {
                $lng = $input[0];
            }
        }

        if (null !== $lat || null !== $lng) {
            return new self($lat, $lng);
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize LatLng from input %s.', json_encode($input)));
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
