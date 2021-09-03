<?php

declare(strict_types=1);

namespace Geokit;

use Generator;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider distanceHaversineDataProvider
     */
    public function testDistanceHaversine(Position $pos1, Position $pos2, float $distance): void
    {
        self::assertEqualsWithDelta(
            $distance,
            distanceHaversine($pos1, $pos2)->meters(),
            0.0001
        );
    }

    public static function distanceHaversineDataProvider(): Generator
    {
        yield [
            Position::fromXY(60.463472083210945, 44.65105198323727),
            Position::fromXY(83.73959356918931, -35.21140778437257),
            9185291.4233,
        ];

        yield [
            Position::fromXY(-100.69272816181183, 85.67559066228569),
            Position::fromXY(-169.56520546227694, 8.659202512353659),
            8873933.2562,
        ];

        yield [
            Position::fromXY(86.67973218485713, -61.20406142435968),
            Position::fromXY(-112.75070607662201, -46.86954100616276),
            7871751.1082,
        ];

        yield [
            Position::fromXY(114.92620809003711, 19.441748214885592),
            Position::fromXY(5.652987342327833, 82.39083864726126),
            8141668.5354,
        ];

        yield [
            Position::fromXY(71.53828611597419, -15.120142288506031),
            Position::fromXY(176.72984121367335, -28.01164012402296),
            10651065.6175,
        ];

        yield [
            Position::fromXY(69.48629681020975, -30.777964973822236),
            Position::fromXY(-13.63121923059225, 8.096220837906003),
            9817278.1798,
        ];

        yield [
            Position::fromXY(-144.45135304704309, -69.95015325956047),
            Position::fromXY(-115.67441381514072, 23.054808229207993),
            10590559.7265,
        ];
    }

    /**
     * @dataProvider distanceVincentyDataProvider
     */
    public function testDistanceVincenty(Position $pos1, Position $pos2, float $distance): void
    {
        self::assertEqualsWithDelta(
            $distance,
            distanceVincenty($pos1, $pos2)->meters(),
            0.0001
        );
    }

    public static function distanceVincentyDataProvider(): Generator
    {
        yield [
            Position::fromXY(60.463472083210945, 44.65105198323727),
            Position::fromXY(83.73959356918931, -35.21140778437257),
            9151350.5841,
        ];

        yield [
            Position::fromXY(-100.69272816181183, 85.67559066228569),
            Position::fromXY(-169.56520546227694, 8.659202512353659),
            8872957.8831,
        ];

        yield [
            Position::fromXY(86.67973218485713, -61.20406142435968),
            Position::fromXY(-112.75070607662201, -46.86954100616276),
            7896462.2245,
        ];

        yield [
            Position::fromXY(114.92620809003711, 19.441748214885592),
            Position::fromXY(5.652987342327833, 82.39083864726126),
            8148846.7071,
        ];

        yield [
            Position::fromXY(71.53828611597419, -15.120142288506031),
            Position::fromXY(176.72984121367335, -28.01164012402296),
            10666157.5230,
        ];

        yield [
            Position::fromXY(69.48629681020975, -30.777964973822236),
            Position::fromXY(-13.63121923059225, 8.096220837906003),
            9818690.27471,
        ];

        yield [
            Position::fromXY(-144.45135304704309, -69.95015325956047),
            Position::fromXY(-115.67441381514072, 23.054808229207993),
            10564410.1591,
        ];
    }

    public function testDistanceHaversineCoIncidentPositions(): void
    {
        self::assertEquals(
            0,
            distanceVincenty(Position::fromXY(90, 90), Position::fromXY(90, 90))->meters()
        );
    }

    public function testDistanceHaversineShouldNotConvergeForHalfTripAroundEquator(): void
    {
        $this->expectException(Exception\RuntimeException::class);
        $this->expectExceptionMessage('Vincenty formula failed to converge.');

        distanceVincenty(Position::fromXY(0, 0), Position::fromXY(180, 0));
    }

    public function testHeading(): void
    {
        self::assertEquals(90, heading(Position::fromXY(0, 0), Position::fromXY(1, 0)));
        self::assertEquals(0, heading(Position::fromXY(0, 0), Position::fromXY(0, 1)));
        self::assertEquals(270, heading(Position::fromXY(0, 0), Position::fromXY(-1, 0)));
        self::assertEquals(180, heading(Position::fromXY(0, 0), Position::fromXY(0, -1)));
    }

    public function testMidpoint(): void
    {
        $midpoint = midpoint(
            Position::fromXY(-96.958444, 32.918593),
            Position::fromXY(-96.990159, 32.969527)
        );

        self::assertEquals(
            32.94406100147102,
            $midpoint->latitude()
        );
        self::assertEquals(
            -96.974296932499726,
            $midpoint->longitude()
        );
    }

    public function testEndpoint(): void
    {
        $endpoint = endpoint(
            Position::fromXY(-96.958444, 32.918593),
            332,
            new Distance(6389.09568)
        );

        self::assertEquals(
            32.96932167481445,
            $endpoint->latitude()
        );
        self::assertEquals(
            -96.99059694331415,
            $endpoint->longitude()
        );
    }

    public function testCircle(): void
    {
        $center = Position::fromXY(-75.343, 39.984);
        $distance = Distance::fromString('50km');

        $circle = circle(
            $center,
            $distance,
            32
        );

        self::assertCount(33, $circle);

        self::assertTrue($circle->contains($center));

        /** @var Position $point */
        foreach ($circle as $point) {
            self::assertEqualsWithDelta(
                $distance->meters(),
                distanceHaversine($center, $point)->meters(),
                0.001
            );
        }
    }

    /**
     * @dataProvider normalizeLatDataProvider
     */
    public function testNormalizeLat(float $a, float $b): void
    {
        self::assertEquals($b, normalizeLatitude($a));
    }

    public function normalizeLatDataProvider(): Generator
    {
        yield [-365, -5];
        yield [-185, 5];
        yield [-95, -85];
        yield [-90, -90];
        yield [5, 5];
        yield [90, 90];
        yield [100, 80];
        yield [185, -5];
        yield [365, 5];
    }

    /**
     * @dataProvider normalizeLngDataProvider
     */
    public function testNormalizeLng(float $a, float $b): void
    {
        self::assertEquals($b, normalizeLongitude($a));
    }

    public function normalizeLngDataProvider(): Generator
    {
        yield [-545, 175];
        yield [-365, -5];
        yield [-360, 0];
        yield [-185, 175];
        yield [-180, 180];
        yield [5, 5];
        yield [180, 180];
        yield [215, -145];
        yield [360, 0];
        yield [395, 35];
        yield [540, 180];
    }
}
