<?php

namespace Geokit;

use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testConstructor()
    {
        $position = new Position(1.0, 2.0);

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testConstructorWithInts()
    {
        $position = new Position(1, 2);

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testNormalizesLatitudeAndLongitude()
    {
        $position = new Position(181, 91);

        $this->assertSame(181.0, $position->x());
        $this->assertSame(91.0, $position->y());
        $this->assertSame(-179.0, $position->longitude());
        $this->assertSame(90.0, $position->latitude());
    }

    public function testFromCoordinatesWithArray()
    {
        $position = Position::fromCoordinates([1, 2]);

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesWithIterator()
    {
        $position = Position::fromCoordinates(new \ArrayIterator([1, 2]));

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesWithGenerator()
    {
        $position = Position::fromCoordinates((function () {
            yield 1;
            yield 2;
        })());

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesThrowsExceptionForMissingXCoordinate()
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        Position::fromCoordinates([]);
    }

    public function testFromCoordinatesThrowsExceptionForInvalidXCoordinate()
    {
        $this->expectException(Exception\InvalidCoordinateException::class);

        Position::fromCoordinates(['foo', 2]);
    }

    public function testFromCoordinatesThrowsExceptionForMissingYCoordinate()
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        Position::fromCoordinates([1]);
    }

    public function testFromCoordinatesThrowsExceptionForInvalidYCoordinate()
    {
        $this->expectException(Exception\InvalidCoordinateException::class);

        Position::fromCoordinates([1, 'foo']);
    }

    public function testToCoordinates()
    {
        $position = new Position(1, 2);

        $this->assertSame([1.0, 2.0], $position->toCoordinates());
    }

    public function testJsonSerialize()
    {
        $position = new Position(1.1, 2);

        $this->assertSame('[1.1,2]', \json_encode($position));
    }
}
