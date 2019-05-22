<?php

namespace Geokit;

class BoundingBoxTest extends TestCase
{
    protected function assertBoundingBox(BoundingBox $b, float $s, float $w, float $n, float $e): void
    {
        self::assertEquals($s, $b->southWest()->latitude());
        self::assertEquals($w, $b->southWest()->longitude());
        self::assertEquals($n, $b->northEast()->latitude());
        self::assertEquals($e, $b->northEast()->longitude());
    }

    public function testConstructorShouldAcceptPositionsAsFirstAndSecondArgument(): void
    {
        $bbox = new BoundingBox(new Position(1.1234, 2.5678), new Position(3.1234, 4.5678));

        self::assertEquals(1.1234, $bbox->southWest()->longitude());
        self::assertEquals(2.5678, $bbox->southWest()->latitude());

        self::assertEquals(3.1234, $bbox->northEast()->longitude());
        self::assertEquals(4.5678, $bbox->northEast()->latitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate(): void
    {
        $this->expectException(Exception\LogicException::class);
        new BoundingBox(new Position(90, 1), new Position(90, 0));
    }

    public function testFromCoordinatesWithArray(): void
    {
        $bbox = BoundingBox::fromCoordinates([1, 2, 3, 4]);

        self::assertSame(1.0, $bbox->southWest()->x());
        self::assertSame(2.0, $bbox->southWest()->y());
        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());

        self::assertSame(3.0, $bbox->northEast()->x());
        self::assertSame(4.0, $bbox->northEast()->y());
        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
    }

    public function testFromCoordinatesWithIterator(): void
    {
        $bbox = BoundingBox::fromCoordinates(new \ArrayIterator([1, 2, 3, 4]));

        self::assertSame(1.0, $bbox->southWest()->x());
        self::assertSame(2.0, $bbox->southWest()->y());
        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());

        self::assertSame(3.0, $bbox->northEast()->x());
        self::assertSame(4.0, $bbox->northEast()->y());
        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
    }

    public function testFromCoordinatesWithGenerator(): void
    {
        $bbox = BoundingBox::fromCoordinates((/** @return \Generator<float> */ static function (): \Generator {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        })());

        self::assertSame(1.0, $bbox->southWest()->x());
        self::assertSame(2.0, $bbox->southWest()->y());
        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());

        self::assertSame(3.0, $bbox->northEast()->x());
        self::assertSame(4.0, $bbox->northEast()->y());
        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
    }

    public function testFromCoordinatesThrowsExceptionForMissingSouthWestXCoordinate(): void
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        BoundingBox::fromCoordinates([]);
    }

    public function testFromCoordinatesThrowsExceptionForMissingSouthWestYCoordinate(): void
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        BoundingBox::fromCoordinates([1]);
    }

    public function testFromCoordinatesThrowsExceptionForMissingNorthEastXCoordinate(): void
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        BoundingBox::fromCoordinates([1, 2]);
    }

    public function testFromCoordinatesThrowsExceptionForMissingNorthEastYCoordinate(): void
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        BoundingBox::fromCoordinates([1, 2, 3]);
    }

    public function testToCoordinates(): void
    {
        $bbox = new BoundingBox(new Position(1, 2), new Position(3, 4));

        self::assertSame([1.0, 2.0, 3.0, 4.0], $bbox->toCoordinates());
    }

    public function testJsonSerialize(): void
    {
        $bbox = new BoundingBox(new Position(1.1, 2), new Position(3.3, 4));

        self::assertSame('[1.1,2,3.3,4]', \json_encode($bbox));
    }

    public function testGetCenterShouldReturnAPositionObject(): void
    {
        $bbox = new BoundingBox(new Position(1.1234, 2.5678), new Position(3.1234, 4.5678));
        $center = new Position(2.1234, 3.5678);

        self::assertEquals($center, $bbox->center());

        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));
        $center = new Position(180, 0);

        self::assertEquals($center, $bbox->center());
    }

    public function testGetSpanShouldReturnAPositionObject(): void
    {
        $bbox = new BoundingBox(new Position(1.1234, 2.5678), new Position(3.1234, 4.5678));

        $span = new Position(2, 2);

        self::assertEquals($span, $bbox->span());
    }

    public function testContains(): void
    {
        $bbox = new BoundingBox(new Position(37, -122), new Position(38, -123));

        self::assertTrue($bbox->contains(new Position(37, -122)));
        self::assertTrue($bbox->contains(new Position(38, -123)));

        self::assertFalse($bbox->contains(new Position(-12, -70)));
    }

    public function testExtendInACircle(): void
    {
        $bbox = new BoundingBox(new Position(0, 0), new Position(0, 0));
        $bbox = $bbox->extend(new Position(1, 0));
        $bbox = $bbox->extend(new Position(0, 1));
        $bbox = $bbox->extend(new Position(-1, 0));
        $bbox = $bbox->extend(new Position(0, -1));
        self::assertBoundingBox($bbox, -1, -1, 1, 1);
    }

    public function testUnion(): void
    {
        $bbox = new BoundingBox(new Position(-122, 37), new Position(-122, 37));

        $bbox = $bbox->union(new BoundingBox(new Position(123, -38), new Position(-123, 38)));
        self::assertBoundingBox($bbox, -38, 123, 38, -122);
    }

    public function testCrossesAntimeridian(): void
    {
        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));

        self::assertTrue($bbox->crossesAntimeridian());
        self::assertEquals(90, $bbox->span()->latitude());
        self::assertEquals(2, $bbox->span()->longitude());
    }

    public function testCrossesAntimeridianViaExtend(): void
    {
        $bbox = new BoundingBox(new Position(179, -45), new Position(-179, 45));

        $bbox = $bbox->extend(new Position(-180, 90));

        self::assertTrue($bbox->crossesAntimeridian());
        self::assertEquals(45, $bbox->span()->latitude());
        self::assertEquals(2, $bbox->span()->longitude());
    }

    public function testExpand(): void
    {
        $bbox = new BoundingBox(
            new Position(179, -45),
            new Position(-179, 45)
        );

        $expandedBbox = $bbox->expand(
            new Distance(100, Distance::UNIT_KILOMETERS)
        );

        self::assertEquals(
            -45.89932036372454,
            $expandedBbox->southWest()->latitude()
        );
        self::assertEquals(
            177.72811671076983,
            $expandedBbox->southWest()->longitude()
        );
        self::assertEquals(
            45.89932036372454,
            $expandedBbox->northEast()->latitude()
        );
        self::assertEquals(
            -177.72811671076983,
            $expandedBbox->northEast()->longitude()
        );
    }

    public function testShrink(): void
    {
        $bbox = new BoundingBox(
            new Position(178.99872959034192, -45.000898315284132),
            new Position(-178.99872959034192, 45.000898315284132)
        );

        $shrinkedBbox = $bbox->shrink(
            new Distance(100, Distance::UNIT_KILOMETERS)
        );

        self::assertEquals(
            -44.10157795155959,
            $shrinkedBbox->southWest()->latitude()
        );
        self::assertEquals(
            -179.7293671753848,
            $shrinkedBbox->southWest()->longitude()
        );
        self::assertEquals(
            44.10157795155959,
            $shrinkedBbox->northEast()->latitude()
        );
        self::assertEquals(
            179.7293671753848,
            $shrinkedBbox->northEast()->longitude()
        );
    }

    public function testShrinkTooMuch(): void
    {
        $bbox = new BoundingBox(
            new Position(1, 1),
            new Position(1, 1)
        );

        $shrinkedBbox = $bbox->shrink(
            new Distance(100)
        );

        self::assertEquals(
            1,
            $shrinkedBbox->southWest()->latitude()
        );
        self::assertEquals(
            1,
            $shrinkedBbox->southWest()->longitude()
        );
        self::assertEquals(
            1,
            $shrinkedBbox->northEast()->latitude()
        );
        self::assertEquals(
            1,
            $shrinkedBbox->northEast()->longitude()
        );
    }

    public function testToPolygon(): void
    {
        $bbox = new BoundingBox(
            new Position(0, 0),
            new Position(10, 10)
        );

        $polygon = $bbox->toPolygon();

        self::assertCount(5, $polygon);
        self::assertTrue($polygon->isClosed());

        /** @var Position[] $array */
        $array = \iterator_to_array($polygon);

        self::assertEquals(
            0,
            $array[0]->latitude()
        );
        self::assertEquals(
            0,
            $array[0]->longitude()
        );

        self::assertEquals(
            0,
            $array[1]->latitude()
        );
        self::assertEquals(
            10,
            $array[1]->longitude()
        );

        self::assertEquals(
            10,
            $array[2]->latitude()
        );
        self::assertEquals(
            10,
            $array[2]->longitude()
        );

        self::assertEquals(
            10,
            $array[3]->latitude()
        );
        self::assertEquals(
            0,
            $array[3]->longitude()
        );
    }
}
