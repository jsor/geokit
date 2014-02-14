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

use Geokit\Bounds;
use Geokit\LatLng;

/**
 * @covers Geokit\Clustering\AbstractClusterer
 */
class AbstractClustererTest extends \PHPUnit_Framework_TestCase
{
    protected $stub;

    public function setUp()
    {
        parent::setUp();
        $this->stub = $this->getMockForAbstractClass('\Geokit\Clustering\AbstractClusterer');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->stub = null;
    }

    public function testClusterReturnsArrayWithClusterObject()
    {
        $this->stub->expects($this->any())
             ->method('isInCluster')
             ->will($this->returnValue(true));

        $latLng1 = new LatLng(51.151786, 10.415039);
        $latLng2 = new LatLng(49.484677, 8.476724);
        $clustered = $this->stub->cluster(array($latLng1, $latLng2));

        $this->assertInternalType('array', $clustered);
        $this->assertInstanceOf('\Geokit\Clustering\ClusterInterface', $clustered[0]);
    }

    public function testClusterClustersSingleLatLngs()
    {
        $this->stub->expects($this->any())
             ->method('isInCluster')
             ->will($this->returnValue(false));

        $latLng1 = new LatLng(51.151786, 10.415039);
        $latLng2 = new LatLng(49.484677, 8.476724);
        $clustered = $this->stub->cluster(array($latLng1, $latLng2));

        $this->assertInternalType('array', $clustered);
        $this->assertInstanceOf('\Geokit\Clustering\ClusterInterface', $clustered[0]);
        $this->assertInstanceOf('\Geokit\Clustering\ClusterInterface', $clustered[1]);
    }

    public function testClusterReturnsArrayWithTwoLatLngsIfForceClusterIsFalse()
    {
        $this->stub->expects($this->any())
             ->method('isInCluster')
             ->will($this->returnValue(false));

        $latLng1 = new LatLng(51.151786, 10.415039);
        $latLng2 = new LatLng(51.151786, 10.415039);
        $clustered = $this->stub->cluster(array($latLng1, $latLng2), false);

        $this->assertInternalType('array', $clustered);
        $this->assertInstanceOf('\Geokit\LatLng', $clustered[0]);
        $this->assertInstanceOf('\Geokit\LatLng', $clustered[1]);
    }
}
