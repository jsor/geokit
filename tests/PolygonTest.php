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
            new Position(0, 0),
            new Position(1, 1),
            new Position(0, 1),
        ];

        $polygon = new Polygon(...$points);

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
        $polygon = Polygon::fromCoordinates((/** @return Generator<float> */ static function (): Generator {
            yield [1, 2];
            yield [2, 3];
        })());

        self::assertCount(2, $polygon);
    }

    public function testIsClose(): void
    {
        $polygon = new Polygon();

        self::assertFalse($polygon->isClosed());

        $polygon = new Polygon(
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0)
        );

        self::assertFalse($polygon->isClosed());

        $polygon = new Polygon(
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0),
            new Position(0, 0)
        );

        self::assertTrue($polygon->isClosed());
    }

    public function testCloseOpenPolygon(): void
    {
        $polygon = new Polygon(
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0)
        );

        $closedPolygon = $polygon->close();

        $array = iterator_to_array($closedPolygon);

        self::assertEquals(new Position(0, 0), $array[count($closedPolygon) - 1]);
    }

    public function testCloseEmptyPolygon(): void
    {
        $polygon = new Polygon();

        $closedPolygon = $polygon->close();

        self::assertCount(0, $closedPolygon);
    }

    public function testCloseAlreadyClosedPolygon(): void
    {
        $polygon = new Polygon(
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0),
            new Position(0, 0)
        );

        $closedPolygon = $polygon->close();

        $array = iterator_to_array($closedPolygon);

        self::assertEquals(new Position(0, 0), $array[count($closedPolygon) - 1]);
    }

    /**
     * @param array<Position> $polygonPositions
     *
     * @dataProvider containsDataProvider
     */
    public function testContains(array $polygonPositions, Position $position, bool $expected): void
    {
        $polygon = new Polygon(...$polygonPositions);

        self::assertEquals($expected, $polygon->contains($position));
    }

    /**
     * @return array<array<array|Position|bool>>
     */
    public function containsDataProvider(): array
    {
        return [
            // Closed counterclockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0),
                ],
                new Position(0.5, 0.5),
                true,
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0),
                ],
                new Position(1.5, 0.5),
                false,
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0),
                ],
                new Position(-0.5, 0.5),
                false,
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0),
                ],
                new Position(0.5, 1.5),
                false,
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0),
                ],
                new Position(0.5, -0.5),
                false,
            ],

            // Closed clockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0),
                ],
                new Position(0.5, 0.5),
                true,
            ],
            [
                [
                    new Position(1, 1),
                    new Position(3, 2),
                    new Position(2, 3),
                    new Position(1, 1),
                ],
                new Position(1.5, 1.5),
                true,
            ],

            // Open counterclockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                ],
                new Position(0.5, 0.5),
                true,
            ],

            // Open clockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                ],
                new Position(0.5, 0.5),
                true,
            ],
            [
                [
                    new Position(1, 1),
                    new Position(3, 2),
                    new Position(2, 3),
                ],
                new Position(1.5, 1.5),
                true,
            ],

            // Empty polygon
            [
                [],
                new Position(0.5, 0.5),
                false,
            ],
        ];
    }

    public function testToBoundingBox(): void
    {
        $polygon = new Polygon(
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0)
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

        $polygon = new Polygon();

        $polygon->toBoundingBox();
    }

    public function testCountable(): void
    {
        $polygon = new Polygon(
            new Position(0, 0)
        );

        self::assertCount(1, $polygon);
    }

    public function testIterable(): void
    {
        self::assertIsIterable(new Polygon());
    }
}
