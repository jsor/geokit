<?php

namespace Geokit;

class MathTest extends TestCase
{
    /**
     * @dataProvider distanceHaversineDataProvider
     */
    public function testDistanceHaversine($LatLng1, $LatLng2, $distance)
    {
        $math = new Math();

        $this->assertEqualsWithDelta(
            $distance,
            $math->distanceHaversine($LatLng1, $LatLng2)->meters(),
            0.0001
        );
    }

    public static function distanceHaversineDataProvider()
    {
        return [
            [
                new LatLng(44.65105198323727, 60.463472083210945),
                new LatLng(-35.21140778437257, 83.73959356918931),
                9185291.4233
            ],
            [
                new LatLng(85.67559066228569, -100.69272816181183),
                new LatLng(8.659202512353659, -169.56520546227694),
                8873933.2562
            ],
            [
                new LatLng(-61.20406142435968, 86.67973218485713),
                new LatLng(-46.86954100616276, -112.75070607662201),
                7871751.1082
            ],
            [
                new LatLng(19.441748214885592, 114.92620809003711),
                new LatLng(82.39083864726126, 5.652987342327833),
                8141668.5354
            ],
            [
                new LatLng(-15.120142288506031, 71.53828611597419),
                new LatLng(-28.01164012402296, 176.72984121367335),
                10651065.6175
            ],
            [
                new LatLng(-30.777964973822236, 69.48629681020975),
                new LatLng(8.096220837906003, -13.63121923059225),
                9817278.1798
            ],
            [
                new LatLng(-69.95015325956047, -144.45135304704309),
                new LatLng(23.054808229207993, -115.67441381514072),
                10590559.7265
            ],
        ];
    }

    /**
     * @dataProvider distanceVincentyDataProvider
     */
    public function testDistanceVincenty($LatLng1, $LatLng2, $distance)
    {
        $math = new Math();

        $this->assertEqualsWithDelta(
            $distance,
            $math->distanceVincenty($LatLng1, $LatLng2)->meters(),
            0.0001
        );
    }

    public static function distanceVincentyDataProvider()
    {
        return [
            [
                new LatLng(44.65105198323727, 60.463472083210945),
                new LatLng(-35.21140778437257, 83.73959356918931),
                9151350.5841
            ],
            [
                new LatLng(85.67559066228569, -100.69272816181183),
                new LatLng(8.659202512353659, -169.56520546227694),
                8872957.8831
            ],
            [
                new LatLng(-61.20406142435968, 86.67973218485713),
                new LatLng(-46.86954100616276, -112.75070607662201),
                7896462.2245
            ],
            [
                new LatLng(19.441748214885592, 114.92620809003711),
                new LatLng(82.39083864726126, 5.652987342327833),
                8148846.7071
            ],
            [
                new LatLng(-15.120142288506031, 71.53828611597419),
                new LatLng(-28.01164012402296, 176.72984121367335),
                10666157.5230
            ],
            [
                new LatLng(-30.777964973822236, 69.48629681020975),
                new LatLng(8.096220837906003, -13.63121923059225),
                9818690.27471
            ],
            [
                new LatLng(-69.95015325956047, -144.45135304704309),
                new LatLng(23.054808229207993, -115.67441381514072),
                10564410.1591
            ],
        ];
    }

    public function testDistanceHaversineCoIncidentPoints()
    {
        $math = new Math();

        $this->assertEquals(
            \sprintf('%F', 0),
            \sprintf('%F', $math->distanceVincenty(new LatLng(90, 90), new LatLng(90, 90))->meters())
        );
    }

    public function testDistanceHaversineShouldNotConvergeForHalfTripAroundEquator()
    {
        $this->expectException(Exception\RuntimeException::class);
        $this->expectExceptionMessage('Vincenty formula failed to converge.');

        $math = new Math();
        $math->distanceVincenty(new LatLng(0, 0), new LatLng(0, 180));
    }

    public function testHeading()
    {
        $math = new Math();

        $this->assertEquals(90, $math->heading(new LatLng(0, 0), new LatLng(0, 1)));
        $this->assertEquals(0, $math->heading(new LatLng(0, 0), new LatLng(1, 0)));
        $this->assertEquals(270, $math->heading(new LatLng(0, 0), new LatLng(0, -1)));
        $this->assertEquals(180, $math->heading(new LatLng(0, 0), new LatLng(-1, 0)));
    }

    public function testMidpoint()
    {
        $math = new Math();

        $midpoint = $math->midpoint(
            new LatLng(32.918593, -96.958444),
            new LatLng(32.969527, -96.990159)
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

        $endpoint = $math->endpoint(new LatLng(32.918593, -96.958444), 332, new Distance(6389.09568));

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

        $center = new LatLng(39.984, -75.343);
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
