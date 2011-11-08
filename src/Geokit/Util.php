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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class Util
{
    /**
     * The radius of the Earth, in meters, assuming the Earth is a perfect sphere.
     *
     * @see http://en.wikipedia.org/wiki/Earth_radius
     */
    const EARTH_RADIUS = 6378135;

    /**
     * Returns the approximate sea level great circle (Earth) distance between
     * two points using the Haversine formula and assuming an Earth radius of
     * self::EARTH_RADIUS.
     *
     * @param mixed $latLng1
     * @param mixed $latLng2
     * @return float The distance in meters
     */
    public static function distance($latLng1, $latLng2)
    {
        return self::EARTH_RADIUS * self::angularDistance($latLng1, $latLng2);
    }

    /**
     * Returns the angular distance between two points using the Haversine formula.
     *
     * @param mixed $latLng1
     * @param mixed $latLng2
     * @return float The distance in meters
     */
    public static function angularDistance($latLng1, $latLng2)
    {
        $latLng1 = self::normalizeLatLng($latLng1);
        $latLng2 = self::normalizeLatLng($latLng2);

        $phi1 = deg2rad($latLng1->getLatitude());
        $phi2 = deg2rad($latLng2->getLatitude());

        $dPhi = deg2rad($latLng2->getLatitude() - $latLng1->getLatitude());
        $dLmd = deg2rad($latLng2->getLongitude() - $latLng1->getLongitude());

        $a = pow(sin($dPhi / 2), 2) +
                cos($phi1) * cos($phi2) *
                  pow(sin($dLmd / 2), 2);

        return 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Returns the (initial) heading from the first point to the second point in degrees.
     *
     * @param mixed $latLng1
     * @param mixed $latLng2
     * @return float Initial heading in degrees from North
     */
    public static function heading($latLng1, $latLng2)
    {
        $latLng1 = self::normalizeLatLng($latLng1);
        $latLng2 = self::normalizeLatLng($latLng2);

        $lat1 = deg2rad($latLng1->getLatitude());
        $lat2 = deg2rad($latLng2->getLatitude());
        $dLon = deg2rad($latLng2->getLongitude() - $latLng1->getLongitude());

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) -
             sin($lat1) * cos($lat2) * cos($dLon);

        $heading = atan2($y, $x);

        return (rad2deg($heading) + 360) % 360;
    }

    /**
     * @param array|string|\Geokit\LatLng $var
     * @return \Geokit\LatLng
     * @throws \InvalidArgumentException
     */
    public static function normalizeLatLng($var)
    {
        $lat = null;
        $lng = null;

        if ($var instanceof LatLng) {
            $lat = $var->getLatitude();
            $lng = $var->getLongitude();
        } elseif ($var instanceof Geometry\Point) {
            $lat = $var->getY();
            $lng = $var->getX();
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
            }

            if (isset($var['longitude'])) {
                $lng = $var['longitude'];
            } elseif (isset($var['lng'])) {
                $lng = $var['lng'];
            } elseif (isset($var['lon'])) {
                $lng = $var['lon'];
            }
        }

        if (null === $lat || null === $lng) {
            throw new \InvalidArgumentException('Cannot create LatLng');
        }

        return new LatLng(self::normalizeLat($lat), self::normalizeLng($lng));
    }

    /**
     * Normalizes a latitude to the (-90, 90) range. Latitudes above 90 or
     * below -90 are capped, not wrapped.
     *
     * @param float $lat The latitude to normalize, in degrees
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
     * @param float $lng The longitude to normalize, in degrees
     * @return float The longitude, fit within the (-180, 180) range
     */
    public static function normalizeLng($lng)
    {
        if ($lng % 360 === 180) {
            return 180;
        }

        $mod = fmod($lng, 360);

        return $mod < -180 ? $mod + 360 : ($mod > 180 ? $mod - 360 : $mod);
    }
}
