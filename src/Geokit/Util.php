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
     * @param array|string|\Geokit\LatLng|\Geokit\Geometry\Point $var
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
