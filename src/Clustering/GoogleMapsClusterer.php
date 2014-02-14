<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Clustering;

use Geokit\Clustering\AbstractClusterer;
use Geokit\Clustering\ClusterFactoryInterface;
use Geokit\LatLng;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class GoogleMapsClusterer extends AbstractClusterer
{
    /**
     * The maximum zoom level.
     *
     * @var int
     */
    const MAX_ZOOM = 21;

    /**
     * You might wonder where did number 268435456 come from?
     * It is half of the earth circumference in pixels at zoom level 21.
     * You can visualize it by thinking of full map.
     * Full map size is 536870912 x 536870912 pixels.
     * Center of the map in pixel coordinates is 268435456,268435456
     * which in latitude and longitude would be 0,0.
     *
     * @var float
     */
    const OFFSET = 268435456;

    /**
     * OFFSET / pi()
     *
     * @var float
     */
    const RADIUS = 85445659.4471;

    /**
     * The map zoom level.
     *
     * @var int
     */
    protected $mapZoomLevel = 21;

    /**
     * The cluster radius (in pixel).
     *
     * @var int
     */
    protected $clusterRadius = 100;

    /**
     * @param \Geokit\ClusterFactoryInterface $clusterFactory
     * @param integer $mapZoomLevel
     * @param integer $clusterRadius
     */
    public function __construct(ClusterFactoryInterface $clusterFactory = null, $mapZoomLevel = null, $clusterRadius = null)
    {
        if (null !== $mapZoomLevel) {
            $this->setMapZoomLevel($mapZoomLevel);
        }

        if (null !== $clusterRadius) {
            $this->setClusterRadius($clusterRadius);
        }

        parent::__construct($clusterFactory);
    }

    /**
     * Set the map zoom level.
     *
     * @param integer $mapZoomLevel
     * @return GoogleMapsClusterer
     */
    public function setMapZoomLevel($mapZoomLevel)
    {
        $this->mapZoomLevel = $mapZoomLevel;
        return $this;
    }

    /**
     * Get the map zoom level.
     *
     * @return integer
     */
    public function getMapZoomLevel()
    {
        return $this->mapZoomLevel;
    }

    /**
     * Set the cluster radius (in pixel).
     *
     * @param integer $clusterRadius
     * @return GoogleMapsClusterer
     */
    public function setClusterRadius($clusterRadius)
    {
        $this->clusterRadius = $clusterRadius;
        return $this;
    }

    /**
     * Get the cluster radius (in pixel).
     *
     * @return integer
     */
    public function getClusterRadius()
    {
        return $this->clusterRadius;
    }

    /**
     * Check whether $latLng2 is in the same cluster than $latLng1.
     *
     * @param \Geokit\LatLng $latLng1
     * @param \Geokit\LatLng $latLng2
     * @return boolean
     */
    protected function isInCluster(LatLng $latLng1, LatLng $latLng2)
    {
        return $this->getClusterRadius() > $this->calcPixelDistance($latLng1, $latLng2);
    }

    /**
     * Convert longitude to x.
     *
     * @param float $lon
     * @return float
     */
    protected function lonToX($lon)
    {
        return round(self::OFFSET + self::RADIUS * $lon * pi() / 180);
    }

    /**
     * Convert latitude to y.
     *
     * @param float $lat
     * @return float
     */
    protected function latToY($lat)
    {
        return round(self::OFFSET - self::RADIUS *
                log((1 + sin($lat * pi() / 180)) /
                (1 - sin($lat * pi() / 180))) / 2);
    }

    /**
     * Calculate pixel distance between to LatLngs based on the zoom.
     *
     * @param \Geokit\LatLng $latLng1
     * @param \Geokit\LatLng $latLng2
     * @return integer
     */
    protected function calcPixelDistance(LatLng $latLng1, LatLng $latLng2)
    {
        $x1 = $this->lonToX($latLng1->getLongitude());
        $y1 = $this->latToY($latLng1->getLatitude());

        $x2 = $this->lonToX($latLng2->getLongitude());
        $y2 = $this->latToY($latLng2->getLatitude());

        return sqrt(pow($x1 - $x2, 2) + pow($y1 - $y2, 2)) >> (self::MAX_ZOOM - $this->getMapZoomLevel());
    }
}
