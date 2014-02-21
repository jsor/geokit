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

class LatLng
{
    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude  = (float) $latitude;
        $this->longitude = (float) $longitude;
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
     * Takes anything which looks like a point and generates a LatLng object
     * from it.
     *
     * $var can be:
     *
     * 1) A string in the format "1.1234, 2.5678" or "1.1234 2.5678"
     * 2) An array in the format
     *    * ['latitude' => 1.1234, 'longitude' => 2.5678]
     *    * ['lat' => 1.1234, 'lng' => 2.5678]
     *    * ['lat' => 1.1234, 'lon' => 2.5678]
     *    * ['x' => 1.1234, 'y' => 2.5678]
     * 3) a LatLng (which is just passed through as-is)
     *
     * @param  array|string|\Geokit\LatLng $var
     * @return \Geokit\LatLng
     * @throws \InvalidArgumentException
     */
    public static function normalize($var)
    {
        $lat = null;
        $lng = null;

        if ($var instanceof self) {
            $lat = $var->getLatitude();
            $lng = $var->getLongitude();
        } elseif (is_string($var)) {
            if (preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $var, $match)) {
                $lat = $match[1];
                $lng = $match[2];
            }
        } elseif (is_array($var) || $var instanceof \ArrayAccess) {
            if (isset($var['latitude'])) {
                $lat = $var['latitude'];
            } elseif (isset($var['lat'])) {
                $lat = $var['lat'];
            } elseif (isset($var['y'])) {
                $lat = $var['y'];
            }

            if (isset($var['longitude'])) {
                $lng = $var['longitude'];
            } elseif (isset($var['lng'])) {
                $lng = $var['lng'];
            } elseif (isset($var['lon'])) {
                $lng = $var['lon'];
            } elseif (isset($var['x'])) {
                $lng = $var['x'];
            }
        }

        if (null === $lat || null === $lng) {
            throw new \InvalidArgumentException('Cannot create LatLng');
        }

        return new self(Calc::normalizeLat($lat), Calc::normalizeLng($lng));
    }
}
