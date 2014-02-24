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
use Geokit\LngLat;

/**
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

    public function testConstructorShouldAcceptLngLatsAsFirstAndSecondArgument()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $this->assertTrue($bounds->getSouthWest() instanceof LngLat);
        $this->assertEquals(1.1234, $bounds->getSouthWest()->getLongitude());
        $this->assertEquals(2.5678, $bounds->getSouthWest()->getLatitude());

        $this->assertTrue($bounds->getNorthEast() instanceof LngLat);
        $this->assertEquals(3.1234, $bounds->getNorthEast()->getLongitude());
        $this->assertEquals(4.5678, $bounds->getNorthEast()->getLatitude());
    }

    public function testGetCenterShouldReturnALngLatObject()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));
        $center = new LngLat(2.1234, 3.5678);

        $this->assertEquals($center, $bounds->getCenter());

        $bounds = new Bounds(new LngLat(179, -45), new LngLat(-179, 45));
        $center = new LngLat(180, 0);

        $this->assertEquals($center, $bounds->getCenter());
    }

    public function testGetSpanShouldReturnALngLatObject()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $span = new LngLat(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testContainsLngLat()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $span = new LngLat(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testExtendByLngLat()
    {
        $bounds = new Bounds(new LngLat(37, -122), new LngLat(38, -123));

        $this->assertTrue($bounds->containsLngLat(new LngLat(37, -122)));
        $this->assertTrue($bounds->containsLngLat(new LngLat(38, -123)));

        $this->assertFalse($bounds->containsLngLat(new LngLat(-12, -70)));
    }

    public function testExtendByLngLatInACircle()
    {
        $bounds = new Bounds(new LngLat(0, 0), new LngLat(0, 0));
        $bounds = $bounds->extendByLngLat(new LngLat(1, 0));
        $bounds = $bounds->extendByLngLat(new LngLat(0, 1));
        $bounds = $bounds->extendByLngLat(new LngLat(-1, 0));
        $bounds = $bounds->extendByLngLat(new LngLat(0, -1));
        $this->assertBounds($bounds, 1, 1, -1, -1);
    }

    public function testExtendByBounds()
    {
        $bounds = new Bounds(new LngLat(-122, 37), new LngLat(-122, 37));

        $bounds = $bounds->extendByBounds(new Bounds(new LngLat(-123, 38), new LngLat(-70, -12)));
        $this->assertBounds($bounds, 38, -70, -12, -123);
    }

    public function testCrossesAntimeridian()
    {
        $bounds = new Bounds(new LngLat(179, -45), new LngLat(-179, 45));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testCrossesAntimeridianViaExtend()
    {
        $bounds = new Bounds(new LngLat(179, -45), new LngLat(179, -45));

        $bounds = $bounds->extendByLngLat(new LngLat(-179, 45));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }
}
