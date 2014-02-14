<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests\Clustering;

use Geokit\Clustering\GoogleMapsClusterer;
use Geokit\LatLng;

/**
 * @covers Geokit\Clustering\GoogleMapsClusterer
 */
class GoogleMapsClustererTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsMapZoomlevelAndClusterRadius()
    {
        $clusterer = new GoogleMapsClusterer(null, 1, 10);

        $this->assertEquals(1, $clusterer->getMapZoomLevel());
        $this->assertEquals(10, $clusterer->getClusterRadius());
    }

    /**
     *
     * @dataProvider testClusterDataProvider
     */
    public function testCluster($mapZoomLevel, $clusterRadius, $clusterCount)
    {
        $clusterer = new GoogleMapsClusterer(null, $mapZoomLevel, $clusterRadius);

        $clustered = $clusterer->cluster(array(new LatLng(51.151786, 10.415039), new LatLng(49.484677, 8.476724)));

        $this->assertCount($clusterCount, $clustered);
    }

    public static function testClusterDataProvider()
    {
        return array(
            array(21, 100, 2),
            array(20, 100, 2),
            array(19, 100, 2),
            array(18, 100, 2),
            array(17, 100, 2),
            array(16, 100, 2),
            array(15, 100, 2),
            array(14, 100, 2),
            array(13, 100, 2),
            array(12, 100, 2),
            array(11, 100, 2),
            array(10, 100, 2),
            array(9, 100, 2),
            array(8, 100, 2),
            array(7, 100, 2),
            array(6, 100, 2),
            array(5, 100, 1),
            array(4, 100, 1),
            array(3, 100, 1),
            array(2, 100, 1),
            array(1, 100, 1),

            array(21, 200, 2),
            array(21, 300, 2),
            array(21, 400, 2),
            array(21, 500, 2),
            array(21, 600, 2),
            array(21, 700, 2),
            array(21, 800, 2),
            array(21, 900, 2),
            array(21, 1000, 2),
            array(21, 2000, 2),
            array(21, 3000, 2),
            array(21, 4000, 2),
            array(21, 5000, 2),
            array(21, 6000, 2),
            array(21, 7000, 2),
            array(21, 8000, 2),

            array(20, 100, 2),
            array(19, 200, 2),
            array(18, 300, 2),
            array(17, 400, 2),
            array(16, 500, 2),
            array(15, 600, 2),
            array(14, 700, 2),
            array(13, 800, 2),
            array(12, 900, 2),
            array(11, 1000, 2),
            array(10, 2000, 2),
            array(9, 3000, 1),
            array(8, 4000, 1),
            array(7, 5000, 1),
            array(6, 6000, 1),
            array(5, 7000, 1),
            array(4, 8000, 1),
            array(3, 8100, 1),
            array(2, 8200, 1),
            array(1, 8300, 1),
        );
    }
}
