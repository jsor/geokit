<?php

namespace Geokit;

use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testConstructor(): void
    {
        $position = new Position(1.0, 2.0);

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testConstructorWithInts(): void
    {
        $position = new Position(1, 2);

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testNormalizesLatitudeAndLongitude(): void
    {
        $position = new Position(181, 91);

        $this->assertSame(181.0, $position->x());
        $this->assertSame(91.0, $position->y());
        $this->assertSame(-179.0, $position->longitude());
        $this->assertSame(89.0, $position->latitude());
    }

    public function testFromCoordinatesWithArray(): void
    {
        $position = Position::fromCoordinates([1, 2]);

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesWithIterator(): void
    {
        $position = Position::fromCoordinates(new \ArrayIterator([1, 2]));

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesWithGenerator(): void
    {
        $position = Position::fromCoordinates((/** @return \Generator<float> */ static function (): \Generator {
            yield 1;
            yield 2;
        })());

        $this->assertSame(1.0, $position->x());
        $this->assertSame(2.0, $position->y());
        $this->assertSame(1.0, $position->longitude());
        $this->assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesThrowsExceptionForMissingXCoordinate(): void
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        Position::fromCoordinates([]);
    }

    public function testFromCoordinatesThrowsExceptionForMissingYCoordinate(): void
    {
        $this->expectException(Exception\MissingCoordinateException::class);

        Position::fromCoordinates([1]);
    }

    public function testToCoordinates(): void
    {
        $position = new Position(1, 2);

        $this->assertSame([1.0, 2.0], $position->toCoordinates());
    }

    public function testJsonSerialize(): void
    {
        $position = new Position(1.1, 2);

        $this->assertSame('[1.1,2]', \json_encode($position));
    }
}