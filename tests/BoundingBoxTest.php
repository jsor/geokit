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

    public function testConstructorShouldAcceptPositionsAsFirstAndSecondArgument()
    {
        $bbox = new BoundingBox(new Position(1.1234, 2.5678), new Position(3.1234, 4.5678));

        $this->assertEquals(1.1234, $bbox->southWest()->longitude());
        $this->assertEquals(2.5678, $bbox->southWest()->latitude());

        $this->assertEquals(3.1234, $bbox->northEast()->longitude());
        $this->assertEquals(4.5678, $bbox->northEast()->latitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate()
    {
        $this->expectException(Exception\LogicException::class);
        new BoundingBox(new Position(90, 1), new Position(90, 0));
    }

    public function testGetCenterShouldReturnAPositionObject()
    {
        $bbox = new BoundingBox(new Position(1.1234, 2.5678), new Position(3.1234, 4.5678));
        $center = new Position(2.1234, 3.5678);

        $this->assertEquals($center, $bbox->center());

        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));
        $center = new Position(180, 0);

        $this->assertEquals($center, $bbox->center());
    }

    public function testGetSpanShouldReturnAPositionObject()
    {
        $bbox = new BoundingBox(new Position(1.1234, 2.5678), new Position(3.1234, 4.5678));

        $span = new Position(2, 2);

        $this->assertEquals($span, $bbox->span());
    }

    public function testContains()
    {
        $bbox = new BoundingBox(new Position(37, -122), new Position(38, -123));

        $this->assertTrue($bbox->contains(new Position(37, -122)));
        $this->assertTrue($bbox->contains(new Position(38, -123)));

        $this->assertFalse($bbox->contains(new Position(-12, -70)));
    }

    public function testExtendInACircle()
    {
        $bbox = new BoundingBox(new Position(0, 0), new Position(0, 0));
        $bbox = $bbox->extend(new Position(1, 0));
        $bbox = $bbox->extend(new Position(0, 1));
        $bbox = $bbox->extend(new Position(-1, 0));
        $bbox = $bbox->extend(new Position(0, -1));
        $this->assertBoundingBox($bbox, -1, -1, 1, 1);
    }

    public function testUnion()
    {
        $bbox = new BoundingBox(new Position(-122, 37), new Position(-122, 37));

        $bbox = $bbox->union(new BoundingBox(new Position(123, -38), new Position(-123, 38)));
        $this->assertBoundingBox($bbox, -38, 123, 38, -122);
    }

    public function testCrossesAntimeridian()
    {
        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));

        $this->assertTrue($bbox->crossesAntimeridian());
        $this->assertEquals(90, $bbox->span()->latitude());
        $this->assertEquals(2, $bbox->span()->longitude());
    }

    public function testCrossesAntimeridianViaExtend()
    {
        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));

        $bbox = $bbox->extend(new Position(-180, 90));

        $this->assertTrue($bbox->crossesAntimeridian());
        $this->assertEquals(90, $bbox->span()->latitude());
        $this->assertEquals(2, $bbox->span()->longitude());
    }

    public function testExpand()
    {
        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));

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
            new Position(178.99872959034192, -45.000898315284132),
            new Position(-178.99872959034192, 45.000898315284132)
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
            new Position(1, 1),
            new Position(1, 1)
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
            new Position(0, 0),
            new Position(10, 10)
        );

        $polygon = $bbox->toPolygon();

        $this->assertCount(5, $polygon);
        $this->assertTrue($polygon->isClosed());

        /** @var Position[] $array */
        $array = \iterator_to_array($polygon);

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
