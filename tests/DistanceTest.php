<?php

declare(strict_types=1);

namespace Geokit;

use Generator;
use function sprintf;

class DistanceTest extends TestCase
{
    public function testShouldConvertToMeters(): void
    {
        $distance = new Distance(1000);
        self::assertSame(1000.0, $distance->meters());
    }

    public function testShouldConvertToMetersWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(1000.0, $distance->m());
    }

    public function testShouldConvertToKilometers(): void
    {
        $distance = new Distance(1000);
        self::assertSame(1.0, $distance->kilometers());
    }

    public function testShouldConvertToKilometersWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(1.0, $distance->km());
    }

    public function testShouldConvertToMiles(): void
    {
        $distance = new Distance(1000);
        self::assertSame(0.62137119223733395, $distance->miles());
    }

    public function testShouldConvertToMilesWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(0.62137119223733395, $distance->mi());
    }

    public function testShouldConvertToYards(): void
    {
        $distance = new Distance(1000);
        self::assertSame(1093.6132983377079, $distance->yards());
    }

    public function testShouldConvertToYardsWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(1093.6132983377079, $distance->yd());
    }

    public function testShouldConvertToFeet(): void
    {
        $distance = new Distance(1000);
        self::assertSame(3280.8398950131232, $distance->feet());
    }

    public function testShouldConvertToFeetWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(3280.8398950131232, $distance->ft());
    }

    public function testShouldConvertToInches(): void
    {
        $distance = new Distance(1000);
        self::assertSame(39370.078740157485, $distance->inches());
    }

    public function testShouldConvertToInchesWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(39370.078740157485, $distance->in());
    }

    public function testShouldConvertToNauticalMiles(): void
    {
        $distance = new Distance(1000);
        self::assertSame(0.5399568034557235, $distance->nautical());
    }

    public function testShouldConvertToNauticalWithAlias(): void
    {
        $distance = new Distance(1000);
        self::assertSame(0.5399568034557235, $distance->nm());
    }

    public function testShouldThrowExceptionForInvalidUnit(): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid unit "foo".');
        new Distance(1000, 'foo');
    }

    public function testShouldThrowExceptionForNegativeValue(): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The distance must be a positive number, got "-1000".');
        new Distance(-1000);
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString(float $value, string $unit): void
    {
        self::assertEquals(1000, Distance::fromString(sprintf('%.15F%s', $value, $unit))->meters());
        self::assertEquals(1000, Distance::fromString(sprintf('%.15F %s', $value, $unit))->meters(), 'With space');
    }

    public function fromStringDataProvider(): Generator
    {
        yield [
            1000,
            '',
        ];

        yield [
            1000,
            'm',
        ];

        yield [
            1000,
            'meter',
        ];

        yield [
            1000,
            'meters',
        ];

        yield [
            1000,
            'metre',
        ];

        yield [
            1000,
            'metres',
        ];

        yield [
            1,
            'km',
        ];

        yield [
            1,
            'kilometer',
        ];

        yield [
            1,
            'kilometers',
        ];

        yield [
            1,
            'kilometre',
        ];

        yield [
            1,
            'kilometres',
        ];

        yield [
            0.62137119223733,
            'mi',
        ];

        yield [
            0.62137119223733,
            'mile',
        ];

        yield [
            0.62137119223733,
            'miles',
        ];

        yield [
            1093.6132983377079,
            'yd',
        ];

        yield [
            1093.6132983377079,
            'yard',
        ];

        yield [
            1093.6132983377079,
            'yards',
        ];

        yield [
            3280.83989501312336,
            'ft',
        ];

        yield [
            3280.83989501312336,
            'foot',
        ];

        yield [
            3280.83989501312336,
            'feet',
        ];

        yield [
            39370.078740157485,
            '″',
        ];

        yield [
            39370.0787401574856,
            'in',
        ];

        yield [
            39370.0787401574856,
            'inch',
        ];

        yield [
            39370.078740157485,
            'inches',
        ];

        yield [
            0.53995680345572,
            'nm',
        ];

        yield [
            0.53995680345572,
            'nautical',
        ];

        yield [
            0.53995680345572,
            'nauticalmile',
        ];

        yield [
            0.53995680345572,
            'nauticalmiles',
        ];
    }

    public function testFromStringThrowsExceptionForInvalidUnit(): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create Distance from string "1000foo".');
        Distance::fromString('1000foo');
    }

    public function testFromStringThrowsExceptionForNegativeValue(): void
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('The distance must be a positive number, got "-1000.5".');
        Distance::fromString('-1000.5m');
    }
}
