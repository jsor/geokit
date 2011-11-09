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
     * Static factory method to create a Bounds object from a Geometry object.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return \Geokit\Bounds
     */
    public static function fromGeometry(Geometry\GeometryInterface $geometry)
    {
        if ('Point' === $geometry->getGeometryType()) {
            return new self(
                new LatLng($geometry->getY(), $geometry->getX()),
                new LatLng($geometry->getY(), $geometry->getX())
            );
        } else {
            $bounds = null;
            foreach ($geometry->all() as $component) {
                if (null === $bounds) {
                    $bounds = self::fromGeometry($component);
                } else {
                    $bounds->extendByBounds(self::fromGeometry($component));
                }
            }

            return $bounds;
        }
    }

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
            $lng  = Util::normalizeLng($this->southWest->getLongitude() + $span / 2);
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
     * @param LatLng $latLng
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
     * @param Bounds $bounds
     * @return Bounds
     */
    public function extendByBounds(Bounds $bounds)
    {
        $this->extendByLatLng($bounds->getSouthWest());
        $this->extendByLatLng($bounds->getNorthEast());

        return $this;
    }

    /**
     * @return \Geokit\Geometry\Polygon
     */
    public function toGeometry()
    {
        $points = array(
            // bottom left
            new Geometry\Point(
                $this->southWest->getLongitude(),
                $this->southWest->getLatitude()
            ),
            // bottom right
            new Geometry\Point(
                $this->northEast->getLongitude(),
                $this->southWest->getLatitude()
            ),
            // top right
            new Geometry\Point(
                $this->northEast->getLongitude(),
                $this->northEast->getLatitude()
            ),
            // top left
            new Geometry\Point(
                $this->southWest->getLongitude(),
                $this->northEast->getLatitude()
            ),
        );

        return new Geometry\Polygon(
            array(
                new Geometry\LinearRing($points)
            )
        );
    }

    /**
     * Returns whether or not the given line of longitude is inside the bounds.
     *
     * @param float $lng
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
     * @param float $west
     * @param float $east
     * @return float
     */
    protected function lngSpan($west, $east)
    {
        return ($west > $east) ? ($east + 360 - $west) : ($east - $west);
    }
}
