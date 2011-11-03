<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests;

use Geokit\Bounds;
use Geokit\LatLng;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Bounds
 */
class BoundsTest extends \PHPUnit_Framework_TestCase
{
    protected function assertBounds(Bounds $b, $n, $e, $s, $w)
    {
        $this->assertEquals($n, $b->getNorthEast()->getLatitude());
        $this->assertEquals($e, $b->getNorthEast()->getLongitude());
        $this->assertEquals($s, $b->getSouthWest()->getLatitude());
        $this->assertEquals($w, $b->getSouthWest()->getLongitude());
    }

    public function testConstructorShouldAcceptLatLngsAsFirstAndSecondArgument()
    {
        $bounds = new Bounds(new LatLng(1.1234, 2.5678), new LatLng(3.1234, 4.5678));

        $this->assertTrue($bounds->getSouthWest() instanceof LatLng);
        $this->assertEquals(1.1234, $bounds->getSouthWest()->getLatitude());
        $this->assertEquals(2.5678, $bounds->getSouthWest()->getLongitude());

        $this->assertTrue($bounds->getNorthEast() instanceof LatLng);
        $this->assertEquals(3.1234, $bounds->getNorthEast()->getLatitude());
        $this->assertEquals(4.5678, $bounds->getNorthEast()->getLongitude());
    }

    public function testGetCenterShouldReturnALatLngObject()
    {
        $bounds = new Bounds(new LatLng(1.1234, 2.5678), new LatLng(3.1234, 4.5678));
        $center = new LatLng(2.1234, 3.5678);

        $this->assertEquals($center, $bounds->getCenter());

        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));
        $center = new LatLng(0, 180);

        $this->assertEquals($center, $bounds->getCenter());
    }

    public function testGetSpanShouldReturnALatLngObject()
    {
        $bounds = new Bounds(new LatLng(1.1234, 2.5678), new LatLng(3.1234, 4.5678));

        $span = new LatLng(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testContainsLatLng()
    {
        $bounds = new Bounds(new LatLng(1.1234, 2.5678), new LatLng(3.1234, 4.5678));

        $span = new LatLng(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testExtendByLatLng()
    {
        $bounds = new Bounds(new LatLng(37, -122), new LatLng(38, -123));

        $this->assertTrue($bounds->containsLatLng(new LatLng(37, -122)));
        $this->assertTrue($bounds->containsLatLng(new LatLng(38, -123)));

        $this->assertFalse($bounds->containsLatLng(new LatLng(-12, -70)));
    }

    public function testExtendByLatLngInACircle()
    {
        $bounds = new Bounds(new LatLng(0, 0), new LatLng(0, 0));
        $bounds->extendByLatLng(new LatLng(0, 1));
        $bounds->extendByLatLng(new LatLng(1, 0));
        $bounds->extendByLatLng(new LatLng(0, -1));
        $bounds->extendByLatLng(new LatLng(-1, 0));
        $this->assertBounds($bounds, 1, 1, -1, -1);
    }

    public function testExtendByBounds()
    {
        $bounds = new Bounds(new LatLng(37, -122), new LatLng(37, -122));

        $bounds->extendByBounds(new Bounds(new LatLng(38, -123), new LatLng(-12, -70)));
        $this->assertBounds($bounds, 38, -70, -12, -123);
    }

    public function testCrossesAntimeridian()
    {
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testCrossesAntimeridianViaExtend()
    {
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(-45, 179));

        $bounds->extendByLatLng(new LatLng(45, -179));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testToGeometryShouldReturnGeometryPolygonObject()
    {
        $bounds = new Bounds(new LatLng(40.73083, -73.99756), new LatLng(40.741404,  -73.988135));

        $polygon = $bounds->toGeometry();

        $this->assertInstanceOf('\Geokit\Geometry\Polygon', $polygon);
        $this->assertEquals($bounds, $polygon->getBounds());
    }
}
