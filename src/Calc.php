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

class Calc
{
    /**
     * The Earth's equatorial radius in meters, assuming the Earth is a perfect sphere.
     *
     * @see http://en.wikipedia.org/wiki/Earth_radius#Equatorial_radius
     */
    const EARTH_EQUATORIAL_RADIUS = 6378137.0;

    /**
     * The Earth's polar radius.
     *
     * @see http://en.wikipedia.org/wiki/Earth_radius#Polar_radius
     */
    const EARTH_POLAR_RADIUS = 6356752.314245;

    /**
     * The Earth's flattening.
     *
     * @see http://en.wikipedia.org/wiki/Earth_radius#Physics_of_Earth.27s_deformation
     */
    const EARTH_FLATTENING = 0.00335281066475; //1/298.257223563

    /**
     * Returns the approximate sea level great circle (Earth) distance between
     * two points using the Haversine formula and assuming an Earth radius of
     * self::EARTH_EQUATORIAL_RADIUS.
     *
     * @see http://www.movable-type.co.uk/scripts/latlong.html
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return \Geokit\Distance
     */
    public static function distanceHaversine($lat1, $lng1, $lat2, $lng2)
    {
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dLat = $lat2 - $lat1;
        $dLon = $lng2 - $lng1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return new Distance(self::EARTH_EQUATORIAL_RADIUS * $c);
    }

    /**
     * Calculates geodetic distance between two points using
     * Vincenty inverse formula for ellipsoids.
     *
     * @see http://www.movable-type.co.uk/scripts/latlong-vincenty.html
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return \Geokit\Distance
     */
    public static function distanceVincenty($lat1, $lng1, $lat2, $lng2)
    {
        // WGS-84 ellipsoid params
        $a = self::EARTH_EQUATORIAL_RADIUS;
        $b = self::EARTH_POLAR_RADIUS;
        $f = self::EARTH_FLATTENING;

        $L  = deg2rad($lng2 - $lng1);
        $U1 = atan((1 - $f) * tan(deg2rad($lat1)));
        $U2 = atan((1 - $f) * tan(deg2rad($lat2)));

        $sinU1 = sin($U1);
        $cosU1 = cos($U1);
        $sinU2 = sin($U2);
        $cosU2 = cos($U2);

        $lambda    = $L;
        $lambdaP   = 2 * pi();
        $iterLimit = 100;

        do {
          $sinLambda = sin($lambda);
          $cosLambda = cos($lambda);
          $sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) +
                      ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) *
                      ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

          if ($sinSigma == 0) {
              return 0;  // co-incident points
          }

          $cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
          $sigma = atan2($sinSigma, $cosSigma);
          $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
          $cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
          $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;

          $C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
          $lambdaP = $lambda;
          $lambda = $L + (1 - $C) * $f * $sinAlpha *
                    ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
        } while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0);

        if ($iterLimit == 0) {
            return null;  // formula failed to converge
        }

        $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
        $A = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
        $B = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
        $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) -
            $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));
        $s = $b * $A * ($sigma - $deltaSigma);

        return new Distance($s);
    }

    /**
     * Returns the (initial) heading from the first point to the second point in degrees.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float Initial heading in degrees from North
     */
    public static function heading($lat1, $lng1, $lat2, $lng2)
    {
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $dLon = deg2rad($lng2 - $lng1);

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) -
             sin($lat1) * cos($lat2) * cos($dLon);

        $heading = atan2($y, $x);

        return fmod(rad2deg($heading) + 360, 360);
    }
}
