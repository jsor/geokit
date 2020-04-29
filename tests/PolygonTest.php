<?php

declare(strict_types=1);

namespace Geokit;

use ArrayIterator;
use Generator;
use function count;
use function iterator_to_array;

class PolygonTest extends TestCase
{
    public function testConstructorAcceptsPositions(): void
    {
        $points = [
            Position::fromXY(0, 0),
            Position::fromXY(1, 1),
            Position::fromXY(0, 1),
        ];

        $polygon = Polygon::fromPositions(...$points);

        /** @var Position[] $array */
        $array = iterator_to_array($polygon);

        self::assertEquals($points[0], $array[0]);
        self::assertEquals(0, $array[0]->latitude());
        self::assertEquals(1, $array[1]->latitude());
        self::assertEquals(1, $array[2]->latitude());
    }

    public function testFromCoordinatesWithArray(): void
    {
        $polygon = Polygon::fromCoordinates([[1, 2], [2, 3]]);

        self::assertCount(2, $polygon);
    }

    public function testFromCoordinatesWithIterator(): void
    {
        $polygon = Polygon::fromCoordinates(new ArrayIterator([[1, 2], [2, 3]]));

        self::assertCount(2, $polygon);
    }

    public function testFromCoordinatesWithGenerator(): void
    {
        $polygon = Polygon::fromCoordinates((/** @return Generator<array<float>> */ static function (): Generator {
            yield [1, 2];
            yield [2, 3];
        })());

        self::assertCount(2, $polygon);
    }

    public function testClosedPolygonIsClosed(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0),
            Position::fromXY(1, 0),
            Position::fromXY(1, 1),
            Position::fromXY(1, 0),
            Position::fromXY(0, 0)
        );

        self::assertTrue($polygon->isClosed());
    }

    public function testOpenPolygonIsNotClosed(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0),
            Position::fromXY(1, 0),
            Position::fromXY(1, 1),
            Position::fromXY(1, 0)
        );

        self::assertFalse($polygon->isClosed());
    }

    public function testEmptyPolygonIsNotClosed(): void
    {
        $polygon = Polygon::fromPositions();

        self::assertNull($polygon->isClosed());
    }

    public function testCloseOpenPolygon(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0),
            Position::fromXY(1, 0),
            Position::fromXY(1, 1),
            Position::fromXY(1, 0)
        );

        $closedPolygon = $polygon->close();

        $array = iterator_to_array($closedPolygon);

        self::assertEquals(Position::fromXY(0, 0), $array[count($closedPolygon) - 1]);
    }

    public function testCloseEmptyPolygon(): void
    {
        $polygon = Polygon::fromPositions();

        $closedPolygon = $polygon->close();

        self::assertCount(0, $closedPolygon);
    }

    public function testCloseAlreadyClosedPolygon(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0),
            Position::fromXY(1, 0),
            Position::fromXY(1, 1),
            Position::fromXY(1, 0),
            Position::fromXY(0, 0)
        );

        $closedPolygon = $polygon->close();

        $array = iterator_to_array($closedPolygon);

        self::assertEquals(Position::fromXY(0, 0), $array[count($closedPolygon) - 1]);
    }

    /**
     * @param array<Position> $polygonPositions
     *
     * @dataProvider containsDataProvider
     */
    public function testContains(array $polygonPositions, Position $position, bool $expected): void
    {
        $polygon = Polygon::fromPositions(...$polygonPositions);

        self::assertEquals($expected, $polygon->contains($position));
    }

    public function containsDataProvider(): Generator
    {
        // Closed counterclockwise polygons
        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
                Position::fromXY(0, 0),
            ],
            Position::fromXY(0.5, 0.5),
            true,
        ];

        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
                Position::fromXY(0, 0),
            ],
            Position::fromXY(1.5, 0.5),
            false,
        ];

        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
                Position::fromXY(0, 0),
            ],
            Position::fromXY(-0.5, 0.5),
            false,
        ];

        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
                Position::fromXY(0, 0),
            ],
            Position::fromXY(0.5, 1.5),
            false,
        ];

        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
                Position::fromXY(0, 0),
            ],
            Position::fromXY(0.5, -0.5),
            false,
        ];

        // Closed clockwise polygons
        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
                Position::fromXY(0, 0),
            ],
            Position::fromXY(0.5, 0.5),
            true,
        ];

        yield [
            [
                Position::fromXY(1, 1),
                Position::fromXY(3, 2),
                Position::fromXY(2, 3),
                Position::fromXY(1, 1),
            ],
            Position::fromXY(1.5, 1.5),
            true,
        ];

        // Open counterclockwise polygons
        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
            ],
            Position::fromXY(0.5, 0.5),
            true,
        ];

        // Open clockwise polygons
        yield [
            [
                Position::fromXY(0, 0),
                Position::fromXY(0, 1),
                Position::fromXY(1, 1),
                Position::fromXY(1, 0),
            ],
            Position::fromXY(0.5, 0.5),
            true,
        ];

        yield [
            [
                Position::fromXY(1, 1),
                Position::fromXY(3, 2),
                Position::fromXY(2, 3),
            ],
            Position::fromXY(1.5, 1.5),
            true,
        ];

        // Empty polygon
        yield [
            [],
            Position::fromXY(0.5, 0.5),
            false,
        ];
    }

    public function testToBoundingBox(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0),
            Position::fromXY(1, 0),
            Position::fromXY(1, 1),
            Position::fromXY(1, 0)
        );

        $bbox = $polygon->toBoundingBox();

        self::assertEquals(0, $bbox->southWest()->latitude());
        self::assertEquals(0, $bbox->southWest()->longitude());
        self::assertEquals(1, $bbox->northEast()->latitude());
        self::assertEquals(1, $bbox->northEast()->longitude());
    }

    public function testToBoundingBoxThrowsExceptionForEmptyPolygon(): void
    {
        $this->expectException(Exception\LogicException::class);
        $this->expectExceptionMessage('Cannot create a BoundingBox from empty Polygon.');

        $polygon = Polygon::fromPositions();

        $polygon->toBoundingBox();
    }

    public function testCountable(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0)
        );

        self::assertCount(1, $polygon);
    }

    public function testIterable(): void
    {
        self::assertIsIterable(Polygon::fromPositions());
    }

    public function testToWKT(): void
    {
        $polygon = Polygon::fromPositions(
            Position::fromXY(0, 0),
            Position::fromXY(1, 0),
            Position::fromXY(1, 1),
            Position::fromXY(1, 0)
        );

        self::assertSame('POLYGON((0 0, 1 0, 1 1, 1 0, 0 0))', $polygon->toWKT());
    }
}