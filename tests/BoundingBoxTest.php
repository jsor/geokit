<?php

declare(strict_types=1);

namespace Geokit;

use ArrayIterator;
use Generator;
use function iterator_to_array;
use function json_encode;

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
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(1.1234, 2.5678), Position::fromXY(3.1234, 4.5678));

        self::assertEquals(1.1234, $bbox->southWest()->longitude());
        self::assertEquals(2.5678, $bbox->southWest()->latitude());

        self::assertEquals(3.1234, $bbox->northEast()->longitude());
        self::assertEquals(4.5678, $bbox->northEast()->latitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate(): void
    {
        $this->expectException(Exception\LogicException::class);
        BoundingBox::fromCornerPositions(Position::fromXY(90, 1), Position::fromXY(90, 0));
    }

    public function testFromCoordinatesWithArray(): void
    {
        $bbox = BoundingBox::fromCoordinates([1, 2, 3, 4]);

        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());
        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());

        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
    }

    public function testFromCoordinatesWithIterator(): void
    {
        $bbox = BoundingBox::fromCoordinates(new ArrayIterator([1, 2, 3, 4]));

        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());
        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());

        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
    }

    public function testFromCoordinatesWithGenerator(): void
    {
        $bbox = BoundingBox::fromCoordinates((/** @return Generator<float> */ static function (): Generator {
            yield 1;
            yield 2;
            yield 3;
            yield 4;
        })());

        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());
        self::assertSame(1.0, $bbox->southWest()->longitude());
        self::assertSame(2.0, $bbox->southWest()->latitude());

        self::assertSame(3.0, $bbox->northEast()->longitude());
        self::assertSame(4.0, $bbox->northEast()->latitude());
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
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(1, 2), Position::fromXY(3, 4));

        self::assertSame([1.0, 2.0, 3.0, 4.0], $bbox->toCoordinates());
    }

    public function testJsonSerialize(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(1.1, 2), Position::fromXY(3.3, 4));

        self::assertSame('[1.1,2,3.3,4]', json_encode($bbox));
    }

    public function testGetCenterShouldReturnAPositionObject(): void
    {
        $bbox   = BoundingBox::fromCornerPositions(Position::fromXY(1.1234, 2.5678), Position::fromXY(3.1234, 4.5678));
        $center = Position::fromXY(2.1234, 3.5678);

        self::assertEquals($center, $bbox->center());

        $bbox   = BoundingBox::fromCornerPositions(Position::fromXY(179, -45), Position::fromXY(-179, 45));
        $center = Position::fromXY(180, 0);

        self::assertEquals($center, $bbox->center());
    }

    public function testGetSpanShouldReturnAPositionObject(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(1.1234, 2.5678), Position::fromXY(3.1234, 4.5678));

        $span = Position::fromXY(2, 2);

        self::assertEquals($span, $bbox->span());
    }

    public function testContains(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(37, -122), Position::fromXY(38, -123));

        self::assertTrue($bbox->contains(Position::fromXY(37, -122)));
        self::assertTrue($bbox->contains(Position::fromXY(38, -123)));

        self::assertFalse($bbox->contains(Position::fromXY(-12, -70)));
    }

    public function testExtendInACircle(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(0, 0), Position::fromXY(0, 0));
        $bbox = $bbox->extend(Position::fromXY(1, 0));
        $bbox = $bbox->extend(Position::fromXY(0, 1));
        $bbox = $bbox->extend(Position::fromXY(-1, 0));
        $bbox = $bbox->extend(Position::fromXY(0, -1));
        self::assertBoundingBox($bbox, -1, -1, 1, 1);
    }

    public function testUnion(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(-122, 37), Position::fromXY(-122, 37));

        $bbox = $bbox->union(BoundingBox::fromCornerPositions(Position::fromXY(123, -38), Position::fromXY(-123, 38)));
        self::assertBoundingBox($bbox, -38, 123, 38, -122);
    }

    public function testCrossesAntimeridian(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(179, -45), Position::fromXY(-179, 45));

        self::assertTrue($bbox->crossesAntimeridian());
        self::assertEquals(90, $bbox->span()->latitude());
        self::assertEquals(2, $bbox->span()->longitude());
    }

    public function testCrossesAntimeridianViaExtend(): void
    {
        $bbox = BoundingBox::fromCornerPositions(Position::fromXY(179, -45), Position::fromXY(-179, 45));

        $bbox = $bbox->extend(Position::fromXY(-180, 90));

        self::assertTrue($bbox->crossesAntimeridian());
        self::assertEquals(45, $bbox->span()->latitude());
        self::assertEquals(2, $bbox->span()->longitude());
    }

    public function testExpand(): void
    {
        $bbox = BoundingBox::fromCornerPositions(
            Position::fromXY(179, -45),
            Position::fromXY(-179, 45)
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
        $bbox = BoundingBox::fromCornerPositions(
            Position::fromXY(178.99872959034192, -45.000898315284132),
            Position::fromXY(-178.99872959034192, 45.000898315284132)
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
        $bbox = BoundingBox::fromCornerPositions(
            Position::fromXY(1, 1),
            Position::fromXY(1, 1)
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
        $bbox = BoundingBox::fromCornerPositions(
            Position::fromXY(0, 0),
            Position::fromXY(10, 10)
        );

        $polygon = $bbox->toPolygon();

        self::assertCount(5, $polygon);

        /** @var Position[] $array */
        $array = iterator_to_array($polygon);

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
