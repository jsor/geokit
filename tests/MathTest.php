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
            0.001
        );
    }

    public static function distanceHaversineDataProvider()
    {
        return [
            [
                new LatLng(44.65105198323727, 60.463472083210945),
                new LatLng(-35.21140778437257, 83.73959356918931),
                9195568.382
            ],
            [
                new LatLng(85.67559066228569, -100.69272816181183),
                new LatLng(8.659202512353659, -169.56520546227694),
                8883861.851
            ],
            [
                new LatLng(-61.20406142435968, 86.67973218485713),
                new LatLng(-46.86954100616276, -112.75070607662201),
                7880558.412
            ],
            [
                new LatLng(19.441748214885592, 114.92620809003711),
                new LatLng(82.39083864726126, 5.652987342327833),
                8150777.837
            ],
            [
                new LatLng(-15.120142288506031, 71.53828611597419),
                new LatLng(-28.01164012402296, 176.72984121367335),
                10662982.556
            ],
            [
                new LatLng(-30.777964973822236, 69.48629681020975),
                new LatLng(8.096220837906003, -13.63121923059225),
                9828262.236
            ],
            [
                new LatLng(-69.95015325956047, -144.45135304704309),
                new LatLng(23.054808229207993, -115.67441381514072),
                10602408.968
            ],
            [
                new LatLng(-69.93624382652342, -36.89967077225447),
                new LatLng(53.617225270718336, 129.4124977849424),
                18093996.375
            ],
            [
                new LatLng(-42.07365347072482, 176.21298506855965),
                new LatLng(-54.178582448512316, 81.13013628870249),
                6643416.713
            ],
            [
                new LatLng(-62.89867605082691, 3.9036516286432743),
                new LatLng(44.33718089014292, -49.99945605173707),
                12855031.249
            ],
            [
                new LatLng(-50.42842207476497, 139.02099810540676),
                new LatLng(62.958827102556825, -141.98338044807315),
                14376303.263
            ],
            [
                new LatLng(62.66255331225693, -20.930572282522917),
                new LatLng(-30.06355374120176, -36.294518653303385),
                10412954.165
            ],
            [
                new LatLng(-80.98694069311023, 24.97568277642131),
                new LatLng(3.151013720780611, 78.1766682676971),
                9767329.138
            ],
            [
                new LatLng(-42.36720213666558, 28.86812360957265),
                new LatLng(-46.722429636865854, 152.25086364895105),
                8656766.959
            ],
            [
                new LatLng(0.13397407718002796, -176.59395882859826),
                new LatLng(-87.33300279825926, -171.5560189448297),
                9737926.976
            ],
            [
                new LatLng(-51.00516985170543, -167.77878485620022),
                new LatLng(10.981508698314428, 132.4349656328559),
                8975708.972
            ],
            [
                new LatLng(-22.765081711113453, 134.544414896518),
                new LatLng(63.991813687607646, 20.827705543488264),
                13435195.027
            ],
            [
                new LatLng(65.62432050704956, 175.91195974498987),
                new LatLng(-63.84489024057984, -122.14013747870922),
                15257145.064
            ],
            [
                new LatLng(-44.645075937733054, 140.418138820678),
                new LatLng(-76.16916658356786, -30.36532362923026),
                6572209.165
            ],
            [
                new LatLng(65.00346723943949, -173.44978725537658),
                new LatLng(54.71282500773668, -123.0979248136282),
                2940927.117
            ]
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
            0.001
        );
    }

    public static function distanceVincentyDataProvider()
    {
        return [
            [
                new LatLng(44.65105198323727, 60.463472083210945),
                new LatLng(-35.21140778437257, 83.73959356918931),
                9151350.584
            ],
            [
                new LatLng(85.67559066228569, -100.69272816181183),
                new LatLng(8.659202512353659, -169.56520546227694),
                8872957.883
            ],
            [
                new LatLng(-61.20406142435968, 86.67973218485713),
                new LatLng(-46.86954100616276, -112.75070607662201),
                7896462.224
            ],
            [
                new LatLng(19.441748214885592, 114.92620809003711),
                new LatLng(82.39083864726126, 5.652987342327833),
                8148846.707
            ],
            [
                new LatLng(-15.120142288506031, 71.53828611597419),
                new LatLng(-28.01164012402296, 176.72984121367335),
                10666157.523
            ],
            [
                new LatLng(-30.777964973822236, 69.48629681020975),
                new LatLng(8.096220837906003, -13.63121923059225),
                9818690.274
            ],
            [
                new LatLng(-69.95015325956047, -144.45135304704309),
                new LatLng(23.054808229207993, -115.67441381514072),
                10564410.159
            ],
            [
                new LatLng(-69.93624382652342, -36.89967077225447),
                new LatLng(53.617225270718336, 129.4124977849424),
                18058852.199
            ],
            [
                new LatLng(-42.07365347072482, 176.21298506855965),
                new LatLng(-54.178582448512316, 81.13013628870249),
                6654712.650668
            ],
            [
                new LatLng(-62.89867605082691, 3.9036516286432743),
                new LatLng(44.33718089014292, -49.99945605173707),
                12810693.042278
            ],
            [
                new LatLng(-50.42842207476497, 139.02099810540676),
                new LatLng(62.958827102556825, -141.98338044807315),
                14334895.461358
            ],
            [
                new LatLng(62.66255331225693, -20.930572282522917),
                new LatLng(-30.06355374120176, -36.294518653303385),
                10369161.033963
            ],
            [
                new LatLng(-80.98694069311023, 24.97568277642131),
                new LatLng(3.151013720780611, 78.1766682676971),
                9746495.212962
            ],
            [
                new LatLng(-42.36720213666558, 28.86812360957265),
                new LatLng(-46.722429636865854, 152.25086364895105),
                8670341.181668
            ],
            [
                new LatLng(0.13397407718002796, -176.59395882859826),
                new LatLng(-87.33300279825926, -171.5560189448297),
                9720046.244631
            ],
            [
                new LatLng(-51.00516985170543, -167.77878485620022),
                new LatLng(10.981508698314428, 132.4349656328559),
                8952091.427135
            ],
            [
                new LatLng(-22.765081711113453, 134.544414896518),
                new LatLng(63.991813687607646, 20.827705543488264),
                13409176.736188
            ],
            [
                new LatLng(65.62432050704956, 175.91195974498987),
                new LatLng(-63.84489024057984, -122.14013747870922),
                15212358.215264
            ],
            [
                new LatLng(-44.645075937733054, 140.418138820678),
                new LatLng(-76.16916658356786, -30.36532362923026),
                6584621.671694
            ],
            [
                new LatLng(65.00346723943949, -173.44978725537658),
                new LatLng(54.71282500773668, -123.0979248136282),
                2947504.722066
            ]
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
        $this->expectException('\RuntimeException', 'Vincenty formula failed to converge.');

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
            $midpoint->getLatitude()
        );
        $this->assertEquals(
            -96.974296932499726,
            $midpoint->getLongitude()
        );
    }

    public function testEndpoint()
    {
        $math = new Math();

        $endpoint = $math->endpoint(new LatLng(32.918593, -96.958444), 332, new Distance(6389.09568));

        $this->assertEquals(
            32.969264985093176,
            $endpoint->getLatitude()
        );
        $this->assertEquals(
            -96.990560988610554,
            $endpoint->getLongitude()
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

    public function testExpandBounds()
    {
        $math = new Math();

        $bounds = $math->expandBounds(
            new Bounds(new LatLng(-45, 179), new LatLng(45, -179)),
            new Distance(100)
        );

        $this->assertEquals(
            -45.000898315284132,
            $bounds->getSouthWest()->getLatitude()
        );
        $this->assertEquals(
            178.99872959034192,
            $bounds->getSouthWest()->getLongitude()
        );
        $this->assertEquals(
            45.000898315284132,
            $bounds->getNorthEast()->getLatitude()
        );
        $this->assertEquals(
            -178.99872959034192,
            $bounds->getNorthEast()->getLongitude()
        );
    }

    public function testShrinkBounds()
    {
        $math = new Math();

        $bounds = $math->shrinkBounds(
            new Bounds(
                new LatLng(-45.000898315284132, 178.99872959034192),
                new LatLng(45.000898315284132, -178.99872959034192)
            ),
            new Distance(100)
        );

        $this->assertEquals(
            -45,
            $bounds->getSouthWest()->getLatitude()
        );
        $this->assertEquals(
            179.0000000199187,
            $bounds->getSouthWest()->getLongitude()
        );
        $this->assertEquals(
            45,
            $bounds->getNorthEast()->getLatitude()
        );
        $this->assertEquals(
            -179.0000000199187,
            $bounds->getNorthEast()->getLongitude()
        );
    }

    public function testShrinkBoundsTooMuch()
    {
        $math = new Math();

        $bounds = $math->shrinkBounds(
            new Bounds(
                new LatLng(1, 1),
                new LatLng(1, 1)
            ),
            new Distance(100)
        );

        $this->assertEquals(
            1,
            $bounds->getSouthWest()->getLatitude()
        );
        $this->assertEquals(
            1,
            $bounds->getSouthWest()->getLongitude()
        );
        $this->assertEquals(
            1,
            $bounds->getNorthEast()->getLatitude()
        );
        $this->assertEquals(
            1,
            $bounds->getNorthEast()->getLongitude()
        );
    }
}
