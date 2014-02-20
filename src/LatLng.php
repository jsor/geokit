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
     * @param \Geokit\LatLng $dest
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
     * @param \Geokit\LatLng $dest
     * @return float Initial heading in degrees from North
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
}
