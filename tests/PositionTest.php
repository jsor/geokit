<?php

namespace Geokit;

use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testConstructor(): void
    {
        $position = new Position(1.0, 2.0);

        self::assertSame(1.0, $position->x());
        self::assertSame(2.0, $position->y());
        self::assertSame(1.0, $position->longitude());
        self::assertSame(2.0, $position->latitude());
    }

    public function testConstructorWithInts(): void
    {
        $position = new Position(1, 2);

        self::assertSame(1.0, $position->x());
        self::assertSame(2.0, $position->y());
        self::assertSame(1.0, $position->longitude());
        self::assertSame(2.0, $position->latitude());
    }

    public function testNormalizesLatitudeAndLongitude(): void
    {
        $position = new Position(181, 91);

        self::assertSame(181.0, $position->x());
        self::assertSame(91.0, $position->y());
        self::assertSame(-179.0, $position->longitude());
        self::assertSame(89.0, $position->latitude());
    }

    public function testFromCoordinatesWithArray(): void
    {
        $position = Position::fromCoordinates([1, 2]);

        self::assertSame(1.0, $position->x());
        self::assertSame(2.0, $position->y());
        self::assertSame(1.0, $position->longitude());
        self::assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesWithIterator(): void
    {
        $position = Position::fromCoordinates(new \ArrayIterator([1, 2]));

        self::assertSame(1.0, $position->x());
        self::assertSame(2.0, $position->y());
        self::assertSame(1.0, $position->longitude());
        self::assertSame(2.0, $position->latitude());
    }

    public function testFromCoordinatesWithGenerator(): void
    {
        $position = Position::fromCoordinates((/** @return \Generator<float> */ static function (): \Generator {
            yield 1;
            yield 2;
        })());

        self::assertSame(1.0, $position->x());
        self::assertSame(2.0, $position->y());
        self::assertSame(1.0, $position->longitude());
        self::assertSame(2.0, $position->latitude());
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

        self::assertSame([1.0, 2.0], $position->toCoordinates());
    }

    public function testJsonSerialize(): void
    {
        $position = new Position(1.1, 2);

        self::assertSame('[1.1,2]', \json_encode($position));
    }
}
