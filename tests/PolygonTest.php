<?php

namespace Geokit;

class PolygonTest extends TestCase
{
    public function testConstructorShouldAcceptArrayOfLatLngs()
    {
        $points = array(
            new LatLng(0, 0),
            new LatLng(1, 1),
            'key' => new LatLng(1, 0)
        );

        $polygon = new Polygon($points);

        $this->assertEquals($points[0], $polygon[0]);
        $this->assertEquals(0, $polygon[0]->getLatitude());
        $this->assertEquals(1, $polygon[1]->getLatitude());
        $this->assertEquals(1, $polygon['key']->getLatitude());
    }

    public function testConstructorThrowsExceptionForInvalidLatLng()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Point at index 0 is not an instance of Geokit\LatLng.');

        new Polygon(array('foo'));
    }

    public function testIsClose()
    {
        $polygon = new Polygon();

        $this->assertFalse($polygon->isClosed());

        $polygon = new Polygon(array(
            new LatLng(0, 0),
            new LatLng(0, 1),
            new LatLng(1, 1),
            new LatLng(1, 0)
        ));

        $this->assertFalse($polygon->isClosed());

        $polygon = new Polygon(array(
            new LatLng(0, 0),
            new LatLng(0, 1),
            new LatLng(1, 1),
            new LatLng(1, 0),
            new LatLng(0, 0)
        ));

        $this->assertTrue($polygon->isClosed());
    }

    public function testCloseOpenPolygon()
    {
        $polygon = new Polygon(array(
            new LatLng(0, 0),
            new LatLng(0, 1),
            new LatLng(1, 1),
            new LatLng(1, 0)
        ));

        $closedPolygon = $polygon->close();

        $this->assertEquals(new LatLng(0, 0), $closedPolygon[count($closedPolygon) - 1]);
    }

    public function testCloseEmptyPolygon()
    {
        $polygon = new Polygon();

        $closedPolygon = $polygon->close();

        $this->assertCount(0, $closedPolygon);
    }

    public function testCloseAlreadyClosedPolygon()
    {
        $polygon = new Polygon(array(
            new LatLng(0, 0),
            new LatLng(0, 1),
            new LatLng(1, 1),
            new LatLng(1, 0),
            new LatLng(0, 0)
        ));

        $closedPolygon = $polygon->close();

        $this->assertEquals(new LatLng(0, 0), $closedPolygon[count($closedPolygon) - 1]);
    }

    /**
     * @dataProvider containsDataProvider
     */
    public function testContains($polygon, $point, $expected)
    {
        $polygon = new Polygon($polygon);

        $this->assertEquals($expected, $polygon->contains(LatLng::normalize($point)));
    }

    public function containsDataProvider()
    {
        return array(
            // Closed counterclockwise polygons
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(1, 0),
                    new LatLng(1, 1),
                    new LatLng(0, 1),
                    new LatLng(0, 0)
                ),
                new LatLng(0.5, 0.5),
                true
            ),
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(1, 0),
                    new LatLng(1, 1),
                    new LatLng(0, 1),
                    new LatLng(0, 0)
                ),
                new LatLng(0.5, 1.5),
                false
            ),
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(1, 0),
                    new LatLng(1, 1),
                    new LatLng(0, 1),
                    new LatLng(0, 0)
                ),
                new LatLng(0.5, -0.5),
                false
            ),
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(1, 0),
                    new LatLng(1, 1),
                    new LatLng(0, 1),
                    new LatLng(0, 0)
                ),
                new LatLng(1.5, 0.5),
                false
            ),
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(1, 0),
                    new LatLng(1, 1),
                    new LatLng(0, 1),
                    new LatLng(0, 0)
                ),
                new LatLng(-0.5, 0.5),
                false
            ),

            // Closed clockwise polygons
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(0, 1),
                    new LatLng(1, 1),
                    new LatLng(1, 0),
                    new LatLng(0, 0)
                ),
                new LatLng(0.5, 0.5),
                true
            ),
            array(
                array(
                    new LatLng(1, 1),
                    new LatLng(2, 3),
                    new LatLng(3, 2),
                    new LatLng(1, 1)
                ),
                new LatLng(1.5, 1.5),
                true
            ),

            // Open counterclockwise polygons
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(1, 0),
                    new LatLng(1, 1),
                    new LatLng(0, 1)
                ),
                new LatLng(0.5, 0.5),
                true
            ),

            // Open clockwise polygons
            array(
                array(
                    new LatLng(0, 0),
                    new LatLng(0, 1),
                    new LatLng(1, 1),
                    new LatLng(1, 0)
                ),
                new LatLng(0.5, 0.5),
                true
            ),
            array(
                array(
                    new LatLng(1, 1),
                    new LatLng(2, 3),
                    new LatLng(3, 2)
                ),
                new LatLng(1.5, 1.5),
                true
            ),

            // Empty polygon
            array(
                array(),
                new LatLng(0.5, 0.5),
                false
            ),

            // Assoc polygon
            array(
                array(
                    'polygon1' => new LatLng(1, 1),
                    'polygon2' => new LatLng(2, 3),
                    'polygon3' => new LatLng(3, 2)
                ),
                new LatLng(1.5, 1.5),
                true
            ),
        );
    }

    public function testToBounds()
    {
        $points = array(
            new LatLng(0, 0),
            new LatLng(0, 1),
            new LatLng(1, 1),
            new LatLng(1, 0)
        );

        $polygon = new Polygon($points);

        $bounds = $polygon->toBounds();

        $this->assertEquals(0, $bounds->getSouthWest()->getLatitude());
        $this->assertEquals(0,  $bounds->getSouthWest()->getLongitude());
        $this->assertEquals(1, $bounds->getNorthEast()->getLatitude());
        $this->assertEquals(1,  $bounds->getNorthEast()->getLongitude());
    }

    public function testToBoundsThrowsExceptionForEmptyPolygon()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Cannot create Bounds from empty Polygon.');

        $polygon = new Polygon();

        $polygon->toBounds();
    }

    public function testArrayAccessNumeric()
    {
        $points = array(
            new LatLng(0, 0)
        );

        $polygon = new Polygon($points);

        $this->assertNotEmpty($polygon[0]);
        $this->assertNotNull($polygon[0]);
        $this->assertEquals($points[0], $polygon[0]);
    }

    public function testArrayAccessAssoc()
    {
        $points = array(
            'key' => new LatLng(0, 0)
        );

        $polygon = new Polygon($points);

        $this->assertNotEmpty($polygon['key']);
        $this->assertNotNull($polygon['key']);
        $this->assertEquals($points['key'], $polygon['key']);
    }

    public function testOffsetGetThrowsExceptionForInvalidKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid offset 0.');

        $polygon = new Polygon();

        $polygon[0];
    }

    public function testOffsetSetThrowsException()
    {
        $this->expectException(\BadMethodCallException::class);

        $points = array(
            new LatLng(0, 0)
        );

        $polygon = new Polygon($points);

        $polygon[0] = new LatLng(1, 1);
    }

    public function testOffsetUnsetThrowsException()
    {
        $this->expectException(\BadMethodCallException::class);

        $points = array(
            new LatLng(0, 0)
        );

        $polygon = new Polygon($points);

        unset($polygon[0]);
    }

    public function testCountable()
    {
        $points = array(
            new LatLng(0, 0)
        );

        $polygon = new Polygon($points);

        $this->assertCount(1, $polygon);
    }

    public function testIteratorAggregate()
    {
        $points = array(
            new LatLng(0, 0)
        );

        $polygon = new Polygon($points);

        foreach ($polygon as $point) {
            $this->assertInstanceOf('Geokit\LatLng', $point);
            return;
        }

        $this->fail('Polygon is not iterable.');
    }
}
