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

use Geokit\LatLng;
use Geokit\Util;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
abstract class AbstractClusterer implements ClustererInterface
{
    /**
     * @var \Geokit\Clustering\ClusterFactoryInterface
     */
    protected $clusterFactory;

    /**
     * @param \Geokit\Clustering\ClusterFactoryInterface $clusterFactory
     */
    public function __construct(ClusterFactoryInterface $clusterFactory = null)
    {
        $this->clusterFactory = $clusterFactory ?: new ClusterFactory();
    }

    /**
     * {@inheritDoc}
     */
    function cluster(array $latLngs, $forceCluster = true)
    {
        $clustered = array();

        // Sort LatLngs by latitude to get smoother results
        usort($latLngs, array($this, 'cmp'));

        // Loop until all LatLngs have been compared
        while (count($latLngs) > 0) {
            $latLng = array_pop($latLngs);
            $latLng = Util::normalizeLatLng($latLng);

            $cluster = null;

            // Compare against all LatLngs which are left
            foreach ($latLngs as $index => $target) {
                $target = Util::normalizeLatLng($target);

                // If two LatLngs are closer than given distance, remove
                // target point from array and add it to cluster
                if ($this->isInCluster($latLng, $target)) {
                    unset($latLngs[$index]);

                    if (null === $cluster) {
                        $cluster = $this->clusterFactory->createCluster();
                        $cluster->add($latLng);
                    }

                    $cluster->add($target);
                }
            }

            // If no cluster has been created yet, create one with a single LatLng
            if (null === $cluster && $forceCluster) {
                $cluster = $this->clusterFactory->createCluster();
                $cluster->add($latLng);
            }

            if (null !== $cluster) {
                $clustered[] = $cluster;
            } else {
                $clustered[] = $latLng;
            }
        }

        return $clustered;
    }

    /**
     * Callback for sorting LatLngs by latitude.
     *
     * @param \Geokit\LatLng $latLng1
     * @param \Geokit\LatLng $latLng2
     * @return int
     */
    protected function cmp($latLng1, $latLng2)
    {
        $latLng1 = Util::normalizeLatLng($latLng1);
        $latLng2 = Util::normalizeLatLng($latLng2);

        if ($latLng1->getLatitude() == $latLng2->getLatitude()) {
            return 0;
        }

        return ($latLng1->getLatitude() < $latLng2->getLatitude()) ? -1 : 1;
    }

    /**
     * Check whether $latLng2 is in the same cluster than $latLng1.
     *
     * @param \Geokit\LatLng $latLng1
     * @param \Geokit\LatLng $latLng2
     * @return boolean
     */
    abstract protected function isInCluster(LatLng $latLng1, LatLng $latLng2);
}
