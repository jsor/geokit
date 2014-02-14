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

interface ClusterFactoryInterface
{
    /**
     * @param array $latLngs
     * @return \Geokit\Clustering\ClusterInterface
     */
    function createCluster(array $latLngs = array());
}
