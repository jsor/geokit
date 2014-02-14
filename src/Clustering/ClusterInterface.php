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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
interface ClusterInterface extends \Countable
{
    /**
     * Add a LatLng to the cluster.
     *
     * @param \Geokit\LatLng $latLng
     * @return \Geokit\Clustering\ClusterInterface
     */
    function add(LatLng $latLng);

    /**
     * @return Bounds
     */
    function getBounds();
}
