<?php

namespace Geokit;

class BoundingBoxTest extends TestCase
{
    /**
     * @param BoundingBox  $b
     * @param float $w
     * @param float $s
     * @param float $e
     * @param float $n
     */
    protected function assertBoundingBox(BoundingBox $b, $s, $w, $n, $e)
    {
        $this->assertEquals($s, $b->southWest()->latitude());
        $this->assertEquals($w, $b->southWest()->longitude());
        $this->assertEquals($n, $b->northEast()->latitude());
        $this->assertEquals($e, $b->northEast()->longitude());
    }

    public function testConstructorShouldAcceptLatLngsAsFirstAndSecondArgument()
    {
        $bbox = new BoundingBox(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $this->assertInstanceOf('\Geokit\LatLng', $bbox->southWest());
        $this->assertEquals(1.1234, $bbox->southWest()->longitude());
        $this->assertEquals(2.5678, $bbox->southWest()->latitude());

        $this->assertInstanceOf('\Geokit\LatLng', $bbox->northEast());
        $this->assertEquals(3.1234, $bbox->northEast()->longitude());
        $this->assertEquals(4.5678, $bbox->northEast()->latitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate()
    {
        $this->expectException(Exception\LogicException::class);
        new BoundingBox(new LatLng(1, 90), new LatLng(0, 90));
    }

    public function testGetCenterShouldReturnALatLngObject()
    {
        $bbox = new BoundingBox(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));
        $center = new LatLng(3.5678, 2.1234);

        $this->assertEquals($center, $bbox->center());

        $bbox = new BoundingBox(new LatLng(-45, 179), new LatLng(45, -179));
        $center = new LatLng(0, 180);

        $this->assertEquals($center, $bbox->center());
    }

    public function testGetSpanShouldReturnALatLngObject()
    {
        $bbox = new BoundingBox(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $span = new LatLng(2, 2);

        $this->assertEquals($span, $bbox->span());
    }

    public function testContains()
    {
        $bbox = new BoundingBox(new LatLng(-122, 37), new LatLng(-123, 38));

        $this->assertTrue($bbox->contains(new LatLng(-122, 37)));
        $this->assertTrue($bbox->contains(new LatLng(-123, 38)));

        $this->assertFalse($bbox->contains(new LatLng(-70, -12)));
    }

    public function testExtendInACircle()
    {
        $bbox = new BoundingBox(new LatLng(0, 0), new LatLng(0, 0));
        $bbox = $bbox->extend(new LatLng(0, 1));
        $bbox = $bbox->extend(new LatLng(1, 0));
        $bbox = $bbox->extend(new LatLng(0, -1));
        $bbox = $bbox->extend(new LatLng(-1, 0));
        $this->assertBoundingBox($bbox, -1, -1, 1, 1);
    }

    public function testUnion()
    {
        $bbox = new BoundingBox(new LatLng(37, -122), new LatLng(37, -122));

        $bbox = $bbox->union(new BoundingBox(new LatLng(-38, 123), new LatLng(38, -123)));
        $this->assertBoundingBox($bbox, -38, 123, 38, -122);
    }

    public function testCrossesAntimeridian()
    {
        $bbox = new BoundingBox(new LatLng(-45, 179), new LatLng(45, -179));

        $this->assertTrue($bbox->crossesAntimeridian());
        $this->assertEquals(90, $bbox->span()->latitude());
        $this->assertEquals(2, $bbox->span()->longitude());
    }

    public function testCrossesAntimeridianViaExtend()
    {
        $bbox = new BoundingBox(new LatLng(-45, 179), new LatLng(45, -179));

        $bbox = $bbox->extend(new LatLng(90, -180));

        $this->assertTrue($bbox->crossesAntimeridian());
        $this->assertEquals(90, $bbox->span()->latitude());
        $this->assertEquals(2, $bbox->span()->longitude());
    }

    public function testExpand()
    {
        $bbox = new BoundingBox(new LatLng(-45, 179), new LatLng(45, -179));

        $expandedBbox = $bbox->expand(new Distance(100));

        $this->assertEquals(
            -45.00089932036373,
            $expandedBbox->southWest()->latitude()
        );
        $this->assertEquals(
            178.99872816894472,
            $expandedBbox->southWest()->longitude()
        );
        $this->assertEquals(
            45.00089932036373,
            $expandedBbox->northEast()->latitude()
        );
        $this->assertEquals(
            -178.99872816894472,
            $expandedBbox->northEast()->longitude()
        );
    }

    public function testShrink()
    {
        $bbox = new BoundingBox(
            new LatLng(-45.000898315284132, 178.99872959034192),
            new LatLng(45.000898315284132, -178.99872959034192)
        );

        $shrinkedBbox = $bbox->shrink(
            new Distance(100)
        );

        $this->assertEquals(
            -44.999998994920404,
            $shrinkedBbox->southWest()->latitude()
        );
        $this->assertEquals(
            179.00000144133816,
            $shrinkedBbox->southWest()->longitude()
        );
        $this->assertEquals(
            44.999998994920404,
            $shrinkedBbox->northEast()->latitude()
        );
        $this->assertEquals(
            -179.00000144133816,
            $shrinkedBbox->northEast()->longitude()
        );
    }

    public function testShrinkTooMuch()
    {
        $bbox = new BoundingBox(
            new LatLng(1, 1),
            new LatLng(1, 1)
        );

        $shrinkedBbox = $bbox->shrink(
            new Distance(100)
        );

        $this->assertEquals(
            1,
            $shrinkedBbox->southWest()->latitude()
        );
        $this->assertEquals(
            1,
            $shrinkedBbox->southWest()->longitude()
        );
        $this->assertEquals(
            1,
            $shrinkedBbox->northEast()->latitude()
        );
        $this->assertEquals(
            1,
            $shrinkedBbox->northEast()->longitude()
        );
    }

    public function testToPolygon()
    {
        $bbox = new BoundingBox(
            new LatLng(0, 0),
            new LatLng(10, 10)
        );

        $polygon = $bbox->toPolygon();

        $this->assertCount(5, $polygon);
        $this->assertTrue($polygon->isClosed());

        /** @var LatLng[] $array */
        $array = iterator_to_array($polygon);

        $this->assertEquals(
            0,
            $array[0]->latitude()
        );
        $this->assertEquals(
            0,
            $array[0]->longitude()
        );

        $this->assertEquals(
            0,
            $array[1]->latitude()
        );
        $this->assertEquals(
            10,
            $array[1]->longitude()
        );

        $this->assertEquals(
            10,
            $array[2]->latitude()
        );
        $this->assertEquals(
            10,
            $array[2]->longitude()
        );

        $this->assertEquals(
            10,
            $array[3]->latitude()
        );
        $this->assertEquals(
            0,
            $array[3]->longitude()
        );
    }
}
