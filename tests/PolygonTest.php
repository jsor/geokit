<?php

namespace Geokit;

class PolygonTest extends TestCase
{
    public function testConstructorShouldAcceptArrayOfLatLngs()
    {
        $points = [
            new Position(0, 0),
            new Position(1, 1),
            'key' => new Position(0, 1)
        ];

        $polygon = new Polygon($points);

        /** @var LatLng[] $array */
        $array = \iterator_to_array($polygon);

        $this->assertEquals($points[0], $array[0]);
        $this->assertEquals(0, $array[0]->latitude());
        $this->assertEquals(1, $array[1]->latitude());
        $this->assertEquals(1, $array['key']->latitude());
    }

    public function testConstructorThrowsExceptionForInvalidPosition()
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Position at index 0 is not an instance of Geokit\Position.');

        new Polygon(['foo']);
    }

    public function testIsClose()
    {
        $polygon = new Polygon();

        $this->assertFalse($polygon->isClosed());

        $polygon = new Polygon([
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0)
        ]);

        $this->assertFalse($polygon->isClosed());

        $polygon = new Polygon([
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0),
            new Position(0, 0)
        ]);

        $this->assertTrue($polygon->isClosed());
    }

    public function testCloseOpenPolygon()
    {
        $polygon = new Polygon([
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0)
        ]);

        $closedPolygon = $polygon->close();

        $array = \iterator_to_array($closedPolygon);

        $this->assertEquals(new Position(0, 0), $array[\count($closedPolygon) - 1]);
    }

    public function testCloseEmptyPolygon()
    {
        $polygon = new Polygon();

        $closedPolygon = $polygon->close();

        $this->assertCount(0, $closedPolygon);
    }

    public function testCloseAlreadyClosedPolygon()
    {
        $polygon = new Polygon([
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0),
            new Position(0, 0)
        ]);

        $closedPolygon = $polygon->close();

        $array = \iterator_to_array($closedPolygon);

        $this->assertEquals(new Position(0, 0), $array[\count($closedPolygon) - 1]);
    }

    /**
     * @dataProvider containsDataProvider
     */
    public function testContains($polygon, $point, $expected)
    {
        $polygon = new Polygon($polygon);

        $this->assertEquals($expected, $polygon->contains($point));
    }

    public function containsDataProvider()
    {
        return [
            // Closed counterclockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0)
                ],
                new Position(0.5, 0.5),
                true
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0)
                ],
                new Position(1.5, 0.5),
                false
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0)
                ],
                new Position(-0.5, 0.5),
                false
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0)
                ],
                new Position(0.5, 1.5),
                false
            ],
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0)
                ],
                new Position(0.5, -0.5),
                false
            ],

            // Closed clockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0),
                    new Position(0, 0)
                ],
                new Position(0.5, 0.5),
                true
            ],
            [
                [
                    new Position(1, 1),
                    new Position(3, 2),
                    new Position(2, 3),
                    new Position(1, 1)
                ],
                new Position(1.5, 1.5),
                true
            ],

            // Open counterclockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0)
                ],
                new Position(0.5, 0.5),
                true
            ],

            // Open clockwise polygons
            [
                [
                    new Position(0, 0),
                    new Position(0, 1),
                    new Position(1, 1),
                    new Position(1, 0)
                ],
                new Position(0.5, 0.5),
                true
            ],
            [
                [
                    new Position(1, 1),
                    new Position(3, 2),
                    new Position(2, 3)
                ],
                new Position(1.5, 1.5),
                true
            ],

            // Empty polygon
            [
                [],
                new Position(0.5, 0.5),
                false
            ],

            // Assoc polygon
            [
                [
                    'polygon1' => new Position(1, 1),
                    'polygon2' => new Position(3, 2),
                    'polygon3' => new Position(2, 3)
                ],
                new Position(1.5, 1.5),
                true
            ],
        ];
    }

    public function testToBoundingBox()
    {
        $points = [
            new Position(0, 0),
            new Position(1, 0),
            new Position(1, 1),
            new Position(1, 0)
        ];

        $polygon = new Polygon($points);

        $bbox = $polygon->toBoundingBox();

        $this->assertEquals(0, $bbox->southWest()->latitude());
        $this->assertEquals(0, $bbox->southWest()->longitude());
        $this->assertEquals(1, $bbox->northEast()->latitude());
        $this->assertEquals(1, $bbox->northEast()->longitude());
    }

    public function testToBoundingBoxThrowsExceptionForEmptyPolygon()
    {
        $this->expectException(Exception\LogicException::class);
        $this->expectExceptionMessage('Cannot create a BoundingBox from empty Polygon.');

        $polygon = new Polygon();

        $polygon->toBoundingBox();
    }

    public function testCountable()
    {
        $points = [
            new Position(0, 0)
        ];

        $polygon = new Polygon($points);

        $this->assertCount(1, $polygon);
    }

    public function testIterable()
    {
        $this->assertIsIterable(new Polygon());
    }
}
