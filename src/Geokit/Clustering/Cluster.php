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

use Geokit\Bounds;
use Geokit\LatLng;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class Cluster implements ClusterInterface
{
    /**
     * @var array
     */
    private $latLngs = array();

    /**
     * @var float
     */
    private $latMin = 90;

    /**
     * @var float
     */
    private $latMax = -90;

    /**
     * @var float
     */
    private $lngMin = 180;

    /**
     * @var float
     */
    private $lngMax = -180;

    /**
     * @param array $latLngs
     */
    public function __construct(array $latLngs = array())
    {
        foreach ($latLngs as $latLng) {
            $this->add($latLng);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function add(LatLng $latLng)
    {
        $this->latLngs[] = $latLng;

        $this->updateMinMax($latLng);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        return $this->latLngs;
    }

    /**
     * {@inheritDoc}
     */
    public function getBounds()
    {
        return new Bounds(
            new LatLng($this->latMin, $this->lngMin),
            new LatLng($this->latMax, $this->lngMax)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->latLngs);
    }

    /**
     * @param \Geokit\LatLng $latLng
     */
    protected function updateMinMax(LatLng $latLng)
    {
        if ($latLng->getLatitude() < $this->latMin) {
            $this->latMin = $latLng->getLatitude();
        }

        if ($latLng->getLongitude() < $this->lngMin) {
            $this->lngMin = $latLng->getLongitude();
        }

        if ($latLng->getLatitude() > $this->latMax) {
            $this->latMax = $latLng->getLatitude();
        }

        if ($latLng->getLongitude() > $this->lngMax) {
            $this->lngMax = $latLng->getLongitude();
        }
    }
}
