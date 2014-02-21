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

class Bounds
{
    /**
     * @var LatLng
     */
    private $southWest;

    /**
     * @var LatLng
     */
    private $northEast;

    /**
     * @param \Geokit\LatLng $southWest
     * @param \Geokit\LatLng $northEast
     */
    public function __construct(LatLng $southWest, LatLng $northEast)
    {
        $this->southWest = $southWest;
        $this->northEast = $northEast;
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getSouthWest()
    {
        return $this->southWest;
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getNorthEast()
    {
        return $this->northEast;
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getCenter()
    {
        if ($this->crossesAntimeridian()) {
            $span = $this->lngSpan($this->southWest->getLongitude(), $this->northEast->getLongitude());
            $lng  = Calc::normalizeLng($this->southWest->getLongitude() + $span / 2);
        } else {
            $lng = ($this->southWest->getLongitude() + $this->northEast->getLongitude()) / 2;
        }

        return new LatLng(
            ($this->southWest->getLatitude() + $this->northEast->getLatitude()) / 2,
            $lng
        );
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getSpan()
    {
        return new LatLng(
            $this->northEast->getLatitude() - $this->southWest->getLatitude(),
            $this->lngSpan($this->southWest->getLongitude(), $this->northEast->getLongitude())
        );
    }

    /**
     * @return boolean
     */
    public function crossesAntimeridian()
    {
        return $this->southWest->getLongitude() > $this->northEast->getLongitude();
    }

    /**
     * @param  \Geokit\LatLng $latLng
     * @return boolean
     */
    public function containsLatLng(LatLng $latLng)
    {
      // check latitude
      if ($this->southWest->getLatitude() > $latLng->getLatitude() ||
          $latLng->getLatitude() > $this->northEast->getLatitude()) {
          return false;
      }

      // check longitude
      return $this->containsLng($latLng->getLongitude());
    }

    /**
     * @param  LatLng $latLng
     * @return Bounds
     */
    public function extendByLatLng(LatLng $latLng)
    {
        $newSouth = min($this->southWest->getLatitude(), $latLng->getLatitude());
        $newNorth = max($this->northEast->getLatitude(), $latLng->getLatitude());

        $newWest = $this->southWest->getLongitude();
        $newEast = $this->northEast->getLongitude();

        if (!$this->containsLng($latLng->getLongitude())) {
            // try extending east and try extending west, and use the one that
            // has the smaller longitudinal span
            $extendEastLngSpan = $this->lngSpan($newWest, $latLng->getLongitude());
            $extendWestLngSpan = $this->lngSpan($latLng->getLongitude(), $newEast);

            if ($extendEastLngSpan <= $extendWestLngSpan) {
                $newEast = $latLng->getLongitude();
            } else {
                $newWest = $latLng->getLongitude();
            }
        }

        $this->southWest = new LatLng($newSouth, $newWest);
        $this->northEast = new LatLng($newNorth, $newEast);

        return $this;
    }

    /**
     *
     * @param  Bounds $bounds
     * @return Bounds
     */
    public function extendByBounds(Bounds $bounds)
    {
        $this->extendByLatLng($bounds->getSouthWest());
        $this->extendByLatLng($bounds->getNorthEast());

        return $this;
    }

    /**
     * Returns whether or not the given line of longitude is inside the bounds.
     *
     * @param  float   $lng
     * @return boolean
     */
    protected function containsLng($lng)
    {
        if ($this->crossesAntimeridian()) {
            return $lng <= $this->northEast->getLongitude() ||
                   $lng >= $this->southWest->getLongitude();
        } else {
            return $this->southWest->getLongitude() <= $lng &&
                   $lng <= $this->northEast->getLongitude();
        }
    }

    /**
     * Gets the longitudinal span of the given west and east coordinates.
     *
     * @param  float $west
     * @param  float $east
     * @return float
     */
    protected function lngSpan($west, $east)
    {
        return ($west > $east) ? ($east + 360 - $west) : ($east - $west);
    }
}
