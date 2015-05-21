<?php

namespace Geokit;

class Math
{
    private $ellipsoid;

    /**
     * @param Ellipsoid|null $ellipsoid
     */
    public function __construct(Ellipsoid $ellipsoid = null)
    {
        $this->ellipsoid = $ellipsoid ?: Ellipsoid::wgs84();
    }

    /**
     * Calculates the approximate sea level great circle (Earth) distance
     * between two points using the Haversine formula.
     *
     * @see http://en.wikipedia.org/wiki/Haversine_formula
     * @see http://www.movable-type.co.uk/scripts/latlong.html
     * @param  mixed    $from
     * @param  mixed    $to
     * @return Distance
     */
    public function distanceHaversine($from, $to)
    {
        $from = LatLng::normalize($from);
        $to = LatLng::normalize($to);

        $lat1 = deg2rad($from->getLatitude());
        $lng1 = deg2rad($from->getLongitude());
        $lat2 = deg2rad($to->getLatitude());
        $lng2 = deg2rad($to->getLongitude());

        $dLat = $lat2 - $lat1;
        $dLon = $lng2 - $lng1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return new Distance($this->ellipsoid->getSemiMajorAxis() * $c);
    }

    /**
     * Calculates the geodetic distance between two points using the
     * Vincenty inverse formula for ellipsoids.
     *
     * @see http://en.wikipedia.org/wiki/Vincenty%27s_formulae
     * @see http://www.movable-type.co.uk/scripts/latlong-vincenty.html
     * @param  mixed             $from
     * @param  mixed             $to
     * @return Distance
     * @throws \RuntimeException
     */
    public function distanceVincenty($from, $to)
    {
        $from = LatLng::normalize($from);
        $to = LatLng::normalize($to);

        $lat1 = $from->getLatitude();
        $lng1 = $from->getLongitude();
        $lat2 = $to->getLatitude();
        $lng2 = $to->getLongitude();

        $a = $this->ellipsoid->getSemiMajorAxis();
        $b = $this->ellipsoid->getSemiMinorAxis();
        $f = $this->ellipsoid->getFlattening();

        $L = deg2rad($lng2 - $lng1);
        $U1 = atan((1 - $f) * tan(deg2rad($lat1)));
        $U2 = atan((1 - $f) * tan(deg2rad($lat2)));

        $sinU1 = sin($U1);
        $cosU1 = cos($U1);
        $sinU2 = sin($U2);
        $cosU2 = cos($U2);

        $lambda = $L;
        $iterLimit = 100;

        do {
            $sinLambda = sin($lambda);
            $cosLambda = cos($lambda);
            $sinSigma = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) +
                ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) *
                ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

            if ($sinSigma == 0) {
                return new Distance(0); // co-incident points
            }

            $cosSigma = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
            $sigma = atan2($sinSigma, $cosSigma);
            $sinAlpha = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
            $cosSqAlpha = 1 - $sinAlpha * $sinAlpha;

            if (0 != $cosSqAlpha) {
                $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
            } else {
                $cos2SigmaM = 0.0; // Equatorial line
            }

            $C = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
            $lambdaP = $lambda;
            $lambda = $L + (1 - $C) * $f * $sinAlpha *
                ($sigma + $C * $sinSigma * ($cos2SigmaM + $C * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
        } while (abs($lambda - $lambdaP) > 1e-12 && --$iterLimit > 0);

        if ($iterLimit == 0) {
            throw new \RuntimeException('Vincenty formula failed to converge.');
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
     * Calculates the (initial) heading from the first point to the second point
     * in degrees.
     *
     * @param  mixed $from
     * @param  mixed $to
     * @return float Initial heading in degrees from North
     */
    public function heading($from, $to)
    {
        $from = LatLng::normalize($from);
        $to = LatLng::normalize($to);

        $lat1 = $from->getLatitude();
        $lng1 = $from->getLongitude();
        $lat2 = $to->getLatitude();
        $lng2 = $to->getLongitude();

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $dLon = deg2rad($lng2 - $lng1);

        $y = sin($dLon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) -
            sin($lat1) * cos($lat2) * cos($dLon);

        $heading = atan2($y, $x);

        return fmod(rad2deg($heading) + 360, 360);
    }

    /**
     * Calculates an intermediate point on the geodesic between the two given
     * points.
     *
     * @see http://www.movable-type.co.uk/scripts/latlong.html
     * @param  mixed  $from
     * @param  mixed  $to
     * @return LatLng
     */
    public function midpoint($from, $to)
    {
        $from = LatLng::normalize($from);
        $to = LatLng::normalize($to);

        $lat1 = $from->getLatitude();
        $lng1 = $from->getLongitude();
        $lat2 = $to->getLatitude();
        $lng2 = $to->getLongitude();

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $dLon = deg2rad($lng2 - $lng1);

        $Bx = cos($lat2) * cos($dLon);
        $By = cos($lat2) * sin($dLon);

        $lat3 = atan2(sin($lat1) + sin($lat2),
            sqrt((cos($lat1) + $Bx) * (cos($lat1) + $Bx) + $By * $By));
        $lon3 = deg2rad($lng1) + atan2($By, cos($lat1) + $Bx);

        return new LatLng(rad2deg($lat3), rad2deg($lon3));
    }

    /**
     * Calculates the destination point along a geodesic, given an initial
     * heading and distance, from the given start point.
     *
     * @see http://www.movable-type.co.uk/scripts/latlong.html
     * @param  mixed  $start
     * @param  float  $heading  (in degrees)
     * @param  mixed  $distance (in meters)
     * @return LatLng
     */
    public function endpoint($start, $heading, $distance)
    {
        $start = LatLng::normalize($start);
        $distance = Distance::normalize($distance);

        $lat = deg2rad($start->getLatitude());
        $lng = deg2rad($start->getLongitude());

        $angularDistance = $distance->meters() / $this->ellipsoid->getSemiMajorAxis();
        $heading = deg2rad($heading);

        $lat2 = asin(sin($lat) * cos($angularDistance) +
            cos($lat) * sin($angularDistance) * cos($heading));
        $lon2 = $lng + atan2(sin($heading) * sin($angularDistance) * cos($lat),
                cos($angularDistance) - sin($lat) * sin($lat2));

        return new LatLng(rad2deg($lat2), rad2deg($lon2));
    }

    /**
     * @param  mixed  $latLngOrBounds
     * @param  mixed  $distance       (in meters)
     * @return Bounds
     */
    public function expand($latLngOrBounds, $distance)
    {
        return $this->transformBounds(
            $latLngOrBounds,
            Distance::normalize($distance)->meters()
        );
    }

    /**
     * @param  mixed  $latLngOrBounds
     * @param  mixed  $distance       (in meters)
     * @return Bounds
     */
    public function shrink($latLngOrBounds, $distance)
    {
        return $this->transformBounds(
            $latLngOrBounds,
            -Distance::normalize($distance)->meters()
        );
    }

    private function transformBounds($input, $distanceInMeters)
    {
        $bounds = Utils::castToBounds($input);

        $latSW = $bounds->getSouthWest()->getLatitude();
        $lngSW = $bounds->getSouthWest()->getLongitude();
        $latNE = $bounds->getNorthEast()->getLatitude();
        $lngNE = $bounds->getNorthEast()->getLongitude();

        $latlngSW = new LatLng(
            $this->latDistance($latSW, $distanceInMeters),
            $this->lngDistance($latSW, $lngSW, $distanceInMeters)
        );

        $latlngNE = new LatLng(
            $this->latDistance($latNE, -$distanceInMeters),
            $this->lngDistance($latNE, $lngNE, -$distanceInMeters)
        );

        // Check if we're shrinking too much
        if ($latlngSW->getLatitude() > $latlngNE->getLatitude()) {
            $center = $bounds->getCenter();

            return new Bounds($center, $center);
        }

        return new Bounds($latlngSW, $latlngNE);
    }

    private function lngDistance($lat1, $lng1, $distanceInMeters)
    {
        $radius = $this->ellipsoid->getSemiMajorAxis();

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);

        $lng2 = ($radius * $lng1 * cos($lat1) - $distanceInMeters) / ($radius * cos($lat1));

        return LatLng::normalizeLng(rad2deg($lng2));
    }

    private function latDistance($lat1, $distanceInMeters)
    {
        $radius = $this->ellipsoid->getSemiMajorAxis();

        $lat1 = deg2rad($lat1);
        $lat2 = ($radius * $lat1 - $distanceInMeters) / $radius;

        return LatLng::normalizeLat(rad2deg($lat2));
    }
}
