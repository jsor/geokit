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

use Geokit\Clustering\ClusterFactory;
use Geokit\LatLng;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Clustering\ClusterFactory
 */
class ClusterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactoryUsesDefaultClass()
    {
        $factory = new ClusterFactory();

        $this->assertInstanceOf('\Geokit\Clustering\Cluster', $factory->createCluster());
    }

    public function testFactoryUsesCustomClass()
    {
        $factory = new ClusterFactory('\Geokit\Tests\Clustering\Fixtures\TestCluster');

        $this->assertInstanceOf('\Geokit\Tests\Clustering\Fixtures\TestCluster', $factory->createCluster());
    }

    public function testFactorySetsDefaultLatLngs()
    {
        $factory = new ClusterFactory();

        $latLng = new LatLng(1.1234, 2.5678);

        $cluster = $factory->createCluster(array($latLng));

        $this->assertCount(1, $cluster);
        $this->assertContains($latLng, $cluster->all());
    }
}
