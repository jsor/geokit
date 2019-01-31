<?php

namespace Geokit;

class MathTest extends TestCase
{
    /**
     * @dataProvider distanceHaversineDataProvider
     */
    public function testDistanceHaversine($pos1, $pos2, $distance)
    {
        $math = new Math();

        $this->assertEqualsWithDelta(
            $distance,
            $math->distanceHaversine($pos1, $pos2)->meters(),
            0.0001
        );
    }

    public static function distanceHaversineDataProvider()
    {
        return [
            [
                new Position(60.463472083210945, 44.65105198323727),
                new Position(83.73959356918931, -35.21140778437257),
                9185291.4233
            ],
            [
                new Position(-100.69272816181183, 85.67559066228569),
                new Position(-169.56520546227694, 8.659202512353659),
                8873933.2562
            ],
            [
                new Position(86.67973218485713, -61.20406142435968),
                new Position(-112.75070607662201, -46.86954100616276),
                7871751.1082
            ],
            [
                new Position(114.92620809003711, 19.441748214885592),
                new Position(5.652987342327833, 82.39083864726126),
                8141668.5354
            ],
            [
                new Position(71.53828611597419, -15.120142288506031),
                new Position(176.72984121367335, -28.01164012402296),
                10651065.6175
            ],
            [
                new Position(69.48629681020975, -30.777964973822236),
                new Position(-13.63121923059225, 8.096220837906003),
                9817278.1798
            ],
            [
                new Position(-144.45135304704309, -69.95015325956047),
                new Position(-115.67441381514072, 23.054808229207993),
                10590559.7265
            ],
        ];
    }

    /**
     * @dataProvider distanceVincentyDataProvider
     */
    public function testDistanceVincenty($pos1, $pos2, $distance)
    {
        $math = new Math();

        $this->assertEqualsWithDelta(
            $distance,
            $math->distanceVincenty($pos1, $pos2)->meters(),
            0.0001
        );
    }

    public static function distanceVincentyDataProvider()
    {
        return [
            [
                new Position(60.463472083210945, 44.65105198323727),
                new Position(83.73959356918931, -35.21140778437257),
                9151350.5841
            ],
            [
                new Position(-100.69272816181183, 85.67559066228569),
                new Position(-169.56520546227694, 8.659202512353659),
                8872957.8831
            ],
            [
                new Position(86.67973218485713, -61.20406142435968),
                new Position(-112.75070607662201, -46.86954100616276),
                7896462.2245
            ],
            [
                new Position(114.92620809003711, 19.441748214885592),
                new Position(5.652987342327833, 82.39083864726126),
                8148846.7071
            ],
            [
                new Position(71.53828611597419, -15.120142288506031),
                new Position(176.72984121367335, -28.01164012402296),
                10666157.5230
            ],
            [
                new Position(69.48629681020975, -30.777964973822236),
                new Position(-13.63121923059225, 8.096220837906003),
                9818690.27471
            ],
            [
                new Position(-144.45135304704309, -69.95015325956047),
                new Position(-115.67441381514072, 23.054808229207993),
                10564410.1591
            ],
        ];
    }

    public function testDistanceHaversineCoIncidentPoints()
    {
        $math = new Math();

        $this->assertEquals(
            0,
            $math->distanceVincenty(new Position(90, 90), new Position(90, 90))->meters()
        );
    }

    public function testDistanceHaversineShouldNotConvergeForHalfTripAroundEquator()
    {
        $this->expectException(Exception\RuntimeException::class);
        $this->expectExceptionMessage('Vincenty formula failed to converge.');

        $math = new Math();
        $math->distanceVincenty(new Position(0, 0), new Position(180, 0));
    }

    public function testHeading()
    {
        $math = new Math();

        $this->assertEquals(90, $math->heading(new Position(0, 0), new Position(1, 0)));
        $this->assertEquals(0, $math->heading(new Position(0, 0), new Position(0, 1)));
        $this->assertEquals(270, $math->heading(new Position(0, 0), new Position(-1, 0)));
        $this->assertEquals(180, $math->heading(new Position(0, 0), new Position(0, -1)));
    }

    public function testMidpoint()
    {
        $math = new Math();

        $midpoint = $math->midpoint(
            new Position(-96.958444, 32.918593),
            new Position(-96.990159, 32.969527)
        );

        $this->assertEquals(
            32.94406100147102,
            $midpoint->latitude()
        );
        $this->assertEquals(
            -96.974296932499726,
            $midpoint->longitude()
        );
    }

    public function testEndpoint()
    {
        $math = new Math();

        $endpoint = $math->endpoint(
            new Position(-96.958444, 32.918593),
            332,
            new Distance(6389.09568)
        );

        $this->assertEquals(
            32.96932167481445,
            $endpoint->latitude()
        );
        $this->assertEquals(
            -96.99059694331415,
            $endpoint->longitude()
        );
    }

    public function testCircle()
    {
        $math = new Math();

        $center = new Position(-75.343, 39.984);
        $distance = Distance::fromString('50km');

        $circle = $math->circle(
            $center,
            $distance,
            32
        );

        $this->assertTrue($circle->isClosed());
        $this->assertCount(33, $circle);

        $this->assertTrue($circle->contains($center));

        foreach ($circle as $point) {
            $this->assertEqualsWithDelta(
                $distance->meters(),
                $math->distanceHaversine($center, $point)->meters(),
                0.001
            );
        }
    }
}
