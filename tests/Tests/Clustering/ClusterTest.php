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

use Geokit\Clustering\Cluster;
use Geokit\Bounds;
use Geokit\LatLng;

/**
 * @covers Geokit\Clustering\Cluster
 */
class ClusterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAddsLatLngs()
    {
        $latLng1 = new LatLng(49.484632, 8.476639);
        $latLng2 = new LatLng(49.401814, 8.680229);

        $cluster = new Cluster(array($latLng1, $latLng2));

        $expected = array($latLng1, $latLng2);

        $this->assertSame($expected, $cluster->all());
        $this->assertCount(2, $cluster);
    }

    public function testGetBoundsAlwaysReturnsBoundsObject()
    {
        $cluster = new Cluster();
        $this->assertInstanceOf('\Geokit\Bounds', $cluster->getBounds());
    }

    public function testAddLatLngUpdatesBoundsObject()
    {
        $cluster = new Cluster();
        $cluster->add(new LatLng(49.484632, 8.476639));
        $cluster->add(new LatLng(49.401814, 8.680229));

        $this->assertNotEquals(90, $cluster->getBounds()->getSouthWest()->getLatitude());
        $this->assertNotEquals(180, $cluster->getBounds()->getSouthWest()->getLongitude());
        $this->assertNotEquals(-90, $cluster->getBounds()->getNorthEast()->getLatitude());
        $this->assertNotEquals(-180, $cluster->getBounds()->getNorthEast()->getLongitude());

        $this->assertEquals(49.401814, $cluster->getBounds()->getSouthWest()->getLatitude());
        $this->assertEquals(8.476639, $cluster->getBounds()->getSouthWest()->getLongitude());
        $this->assertEquals(49.484632, $cluster->getBounds()->getNorthEast()->getLatitude());
        $this->assertEquals(8.680229, $cluster->getBounds()->getNorthEast()->getLongitude());
    }

    public function testAllReturnsArrayOfLatLngs()
    {
        $latLng1 = new LatLng(49.484632, 8.476639);
        $latLng2 = new LatLng(49.401814, 8.680229);

        $cluster = new Cluster();
        $cluster->add($latLng1);
        $cluster->add($latLng2);

        $expected = array($latLng1, $latLng2);

        $this->assertSame($expected, $cluster->all());
    }
}
