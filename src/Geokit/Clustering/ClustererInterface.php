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
interface ClustererInterface
{
    /**
     * Process a collection of LatLngs and cluster them.
     *
     * @param array A collection of LatLngs
     * @param boolean Whether to force a cluster also if it contains a single LatLng
     * @return array An array of clusters
     */
    function cluster(array $latLngs, $forceCluster = true);
}
