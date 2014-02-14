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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class ClusterFactory implements ClusterFactoryInterface
{
    /**
     * @var float
     */
    private $clusterClass = '\Geokit\Clustering\Cluster';

    /**
     * @param string $clusterClass
     */
    public function __construct($clusterClass = null)
    {
        if (null !== $clusterClass) {
            $this->clusterClass = $clusterClass;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createCluster(array $latLngs = array())
    {
        $cluster = new $this->clusterClass();

        foreach ($latLngs as $latLng) {
            $cluster->add($latLng);
        }

        return $cluster;
    }
}
