<?php

namespace Geokit;

class BoundsTest extends TestCase
{
    /**
     * @param Bounds  $b
     * @param integer $w
     * @param integer $s
     * @param integer $e
     * @param integer $n
     */
    protected function assertBounds(Bounds $b, $s, $w, $n, $e)
    {
        $this->assertEquals($s, $b->getSouthWest()->getLatitude());
        $this->assertEquals($w, $b->getSouthWest()->getLongitude());
        $this->assertEquals($n, $b->getNorthEast()->getLatitude());
        $this->assertEquals($e, $b->getNorthEast()->getLongitude());
    }

    public function testConstructorShouldAcceptLatLngsAsFirstAndSecondArgument()
    {
        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $this->assertInstanceOf('\Geokit\LatLng', $bounds->getSouthWest());
        $this->assertEquals(1.1234, $bounds->getSouthWest()->getLongitude());
        $this->assertEquals(2.5678, $bounds->getSouthWest()->getLatitude());

        $this->assertInstanceOf('\Geokit\LatLng', $bounds->getNorthEast());
        $this->assertEquals(3.1234, $bounds->getNorthEast()->getLongitude());
        $this->assertEquals(4.5678, $bounds->getNorthEast()->getLatitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate()
    {
        $this->expectException(\LogicException::class);
        new Bounds(new LatLng(1, 90), new LatLng(0, 90));
    }

    public function testGetCenterShouldReturnALatLngObject()
    {
        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));
        $center = new LatLng(3.5678, 2.1234);

        $this->assertEquals($center, $bounds->getCenter());

        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));
        $center = new LatLng(0, 180);

        $this->assertEquals($center, $bounds->getCenter());
    }

    public function testGetSpanShouldReturnALatLngObject()
    {
        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $span = new LatLng(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testContains()
    {
        $bounds = new Bounds(new LatLng(-122, 37), new LatLng(-123, 38));

        $this->assertTrue($bounds->contains(new LatLng(-122, 37)));
        $this->assertTrue($bounds->contains(new LatLng(-123, 38)));

        $this->assertFalse($bounds->contains(new LatLng(-70, -12)));
    }

    public function testExtendInACircle()
    {
        $bounds = new Bounds(new LatLng(0, 0), new LatLng(0, 0));
        $bounds = $bounds->extend(new LatLng(0, 1));
        $bounds = $bounds->extend(new LatLng(1, 0));
        $bounds = $bounds->extend(new LatLng(0, -1));
        $bounds = $bounds->extend(new LatLng(-1, 0));
        $this->assertBounds($bounds, -1, -1, 1, 1);
    }

    public function testUnion()
    {
        $bounds = new Bounds(new LatLng(37, -122), new LatLng(37, -122));

        $bounds = $bounds->union(new Bounds(new LatLng(-38, 123), new LatLng(38, -123)));
        $this->assertBounds($bounds, -38, 123, 38, -122);
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
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));

        $bounds = $bounds->extend(new LatLng(90, -180));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }
}
