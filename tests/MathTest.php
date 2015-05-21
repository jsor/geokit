<?php

namespace Geokit;

class MathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider distanceHaversineDataProvider
     */
    public function testDistanceHaversine($LatLng1, $LatLng2, $distance)
    {
        $math = new Math();

        $this->assertEquals(
            sprintf('%F', $distance),
            sprintf('%F', $math->distanceHaversine($LatLng1, $LatLng2)->meters())
        );
    }

    public static function distanceHaversineDataProvider()
    {
        return array(
            array(
                array('lat' => 44.65105198323727, 'lng' => 60.463472083210945),
                array('lat' => -35.21140778437257, 'lng' => 83.73959356918931),
                9195568.382018
            ),
            array(
                array('lat' => 85.67559066228569, 'lng' => -100.69272816181183),
                array('lat' => 8.659202512353659, 'lng' => -169.56520546227694),
                8883861.851945
            ),
            array(
                array('lat' => -61.20406142435968, 'lng' => 86.67973218485713),
                array('lat' => -46.86954100616276, 'lng' => -112.75070607662201),
                7880558.412953
            ),
            array(
                array('lat' => 19.441748214885592, 'lng' => 114.92620809003711),
                array('lat' => 82.39083864726126, 'lng' => 5.652987342327833),
                8150777.837176
            ),
            array(
                array('lat' => -15.120142288506031, 'lng' => 71.53828611597419),
                array('lat' => -28.01164012402296, 'lng' => 176.72984121367335),
                10662982.556900
            ),
            array(
                array('lat' => -30.777964973822236, 'lng' => 69.48629681020975),
                array('lat' => 8.096220837906003, 'lng' => -13.63121923059225),
                9828262.236567
            ),
            array(
                array('lat' => -69.95015325956047, 'lng' => -144.45135304704309),
                array('lat' => 23.054808229207993, 'lng' => -115.67441381514072),
                10602408.968987
            ),
            array(
                array('lat' => -69.93624382652342, 'lng' => -36.89967077225447),
                array('lat' => 53.617225270718336, 'lng' => 129.4124977849424),
                18093996.375357
            ),
            array(
                array('lat' => -42.07365347072482, 'lng' => 176.21298506855965),
                array('lat' => -54.178582448512316, 'lng' => 81.13013628870249),
                6643416.713547
            ),
            array(
                array('lat' => -62.89867605082691, 'lng' => 3.9036516286432743),
                array('lat' => 44.33718089014292, 'lng' => -49.99945605173707),
                12855031.249865
            ),
            array(
                array('lat' => -50.42842207476497, 'lng' => 139.02099810540676),
                array('lat' => 62.958827102556825, 'lng' => -141.98338044807315),
                14376303.263602
            ),
            array(
                array('lat' => 62.66255331225693, 'lng' => -20.930572282522917),
                array('lat' => -30.06355374120176, 'lng' => -36.294518653303385),
                10412954.165562
            ),
            array(
                array('lat' => -80.98694069311023, 'lng' => 24.97568277642131),
                array('lat' => 3.151013720780611, 'lng' => 78.1766682676971),
                9767329.138029
            ),
            array(
                array('lat' => -42.36720213666558, 'lng' => 28.86812360957265),
                array('lat' => -46.722429636865854, 'lng' => 152.25086364895105),
                8656766.959287
            ),
            array(
                array('lat' => 0.13397407718002796, 'lng' => -176.59395882859826),
                array('lat' => -87.33300279825926, 'lng' => -171.5560189448297),
                9737926.976940
            ),
            array(
                array('lat' => -51.00516985170543, 'lng' => -167.77878485620022),
                array('lat' => 10.981508698314428, 'lng' => 132.4349656328559),
                8975708.972975
            ),
            array(
                array('lat' => -22.765081711113453, 'lng' => 134.544414896518),
                array('lat' => 63.991813687607646, 'lng' => 20.827705543488264),
                13435195.027854
            ),
            array(
                array('lat' => 65.62432050704956, 'lng' => 175.91195974498987),
                array('lat' => -63.84489024057984, 'lng' => -122.14013747870922),
                15257145.064696
            ),
            array(
                array('lat' => -44.645075937733054, 'lng' => 140.418138820678),
                array('lat' => -76.16916658356786, 'lng' => -30.36532362923026),
                6572209.165628
            ),
            array(
                array('lat' => 65.00346723943949, 'lng' => -173.44978725537658),
                array('lat' => 54.71282500773668, 'lng' => -123.0979248136282),
                2940927.117636
            )
        );
    }

    /**
     * @dataProvider distanceVincentyDataProvider
     */
    public function testDistanceVincenty($LatLng1, $LatLng2, $distance)
    {
        $math = new Math();

        $this->assertEquals(
            sprintf('%F', $distance),
            sprintf('%F', $math->distanceVincenty($LatLng1, $LatLng2)->meters())
        );
    }

    public static function distanceVincentyDataProvider()
    {
        return array(
            array(
                array('lat' => 44.65105198323727, 'lng' => 60.463472083210945),
                array('lat' => -35.21140778437257, 'lng' => 83.73959356918931),
                9151350.584115
            ),
            array(
                array('lat' => 85.67559066228569, 'lng' => -100.69272816181183),
                array('lat' => 8.659202512353659, 'lng' => -169.56520546227694),
                8872957.883065
            ),
            array(
                array('lat' => -61.20406142435968, 'lng' => 86.67973218485713),
                array('lat' => -46.86954100616276, 'lng' => -112.75070607662201),
                7896462.224523
            ),
            array(
                array('lat' => 19.441748214885592, 'lng' => 114.92620809003711),
                array('lat' => 82.39083864726126, 'lng' => 5.652987342327833),
                8148846.707182
            ),
            array(
                array('lat' => -15.120142288506031, 'lng' => 71.53828611597419),
                array('lat' => -28.01164012402296, 'lng' => 176.72984121367335),
                10666157.523016
            ),
            array(
                array('lat' => -30.777964973822236, 'lng' => 69.48629681020975),
                array('lat' => 8.096220837906003, 'lng' => -13.63121923059225),
                9818690.274757
            ),
            array(
                array('lat' => -69.95015325956047, 'lng' => -144.45135304704309),
                array('lat' => 23.054808229207993, 'lng' => -115.67441381514072),
                10564410.159097
            ),
            array(
                array('lat' => -69.93624382652342, 'lng' => -36.89967077225447),
                array('lat' => 53.617225270718336, 'lng' => 129.4124977849424),
                18058852.199871
            ),
            array(
                array('lat' => -42.07365347072482, 'lng' => 176.21298506855965),
                array('lat' => -54.178582448512316, 'lng' => 81.13013628870249),
                6654712.650668
            ),
            array(
                array('lat' => -62.89867605082691, 'lng' => 3.9036516286432743),
                array('lat' => 44.33718089014292, 'lng' => -49.99945605173707),
                12810693.042278
            ),
            array(
                array('lat' => -50.42842207476497, 'lng' => 139.02099810540676),
                array('lat' => 62.958827102556825, 'lng' => -141.98338044807315),
                14334895.461358
            ),
            array(
                array('lat' => 62.66255331225693, 'lng' => -20.930572282522917),
                array('lat' => -30.06355374120176, 'lng' => -36.294518653303385),
                10369161.033963
            ),
            array(
                array('lat' => -80.98694069311023, 'lng' => 24.97568277642131),
                array('lat' => 3.151013720780611, 'lng' => 78.1766682676971),
                9746495.212962
            ),
            array(
                array('lat' => -42.36720213666558, 'lng' => 28.86812360957265),
                array('lat' => -46.722429636865854, 'lng' => 152.25086364895105),
                8670341.181668
            ),
            array(
                array('lat' => 0.13397407718002796, 'lng' => -176.59395882859826),
                array('lat' => -87.33300279825926, 'lng' => -171.5560189448297),
                9720046.244631
            ),
            array(
                array('lat' => -51.00516985170543, 'lng' => -167.77878485620022),
                array('lat' => 10.981508698314428, 'lng' => 132.4349656328559),
                8952091.427135
            ),
            array(
                array('lat' => -22.765081711113453, 'lng' => 134.544414896518),
                array('lat' => 63.991813687607646, 'lng' => 20.827705543488264),
                13409176.736188
            ),
            array(
                array('lat' => 65.62432050704956, 'lng' => 175.91195974498987),
                array('lat' => -63.84489024057984, 'lng' => -122.14013747870922),
                15212358.215264
            ),
            array(
                array('lat' => -44.645075937733054, 'lng' => 140.418138820678),
                array('lat' => -76.16916658356786, 'lng' => -30.36532362923026),
                6584621.671694
            ),
            array(
                array('lat' => 65.00346723943949, 'lng' => -173.44978725537658),
                array('lat' => 54.71282500773668, 'lng' => -123.0979248136282),
                2947504.722066
            )
        );
    }

    public function testDistanceHaversineCoIncidentPoints()
    {
        $math = new Math();

        $this->assertEquals(
            sprintf('%F', 0),
            sprintf('%F', $math->distanceVincenty(new LatLng(90, 90), new LatLng(90, 90))->meters())
        );
    }

    public function testDistanceHaversineShouldNotConvergeForHalfTripAroundEquator()
    {
        $this->setExpectedException('\RuntimeException', 'Vincenty formula failed to converge.');

        $math = new Math();
        $math->distanceVincenty(new LatLng(0, 0), new LatLng(0, 180));
    }

    public function testHeading()
    {
        $math = new Math();

        $this->assertEquals(90, $math->heading(array('lat' => 0, 'lng' => 0), array('lat' => 0, 'lng' => 1)));
        $this->assertEquals(0, $math->heading(array('lat' => 0, 'lng' => 0), array('lat' => 1, 'lng' => 0)));
        $this->assertEquals(270, $math->heading(array('lat' => 0, 'lng' => 0), array('lat' => 0, 'lng' => -1)));
        $this->assertEquals(180, $math->heading(array('lat' => 0, 'lng' => 0), array('lat' => -1, 'lng' => 0)));
    }

    public function testMidpoint()
    {
        $math = new Math();

        $midpoint = $math->midpoint(
            array('lat' => 32.918593, 'lng' => -96.958444),
            array('lat' => 32.969527, 'lng' => -96.990159)
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

        $endpoint = $math->endpoint(array('lat' => 32.918593, 'lng' => -96.958444), 332, new Distance(6389.09568));

        $this->assertEquals(
            32.969264985093176,
            $endpoint->getLatitude()
        );
        $this->assertEquals(
            -96.990560988610554,
            $endpoint->getLongitude()
        );
    }

    public function testExpandWithLatLng()
    {
        $math = new Math();

        $bounds = $math->expand(
            '49.50042565 8.50207515',
            '100 meters'
        );

        $this->assertEquals(
            49.499527334715879,
            $bounds->getSouthWest()->getLatitude()
        );
        $this->assertEquals(
            8.5006919399029339,
            $bounds->getSouthWest()->getLongitude()
        );
        $this->assertEquals(
            49.501323965284122,
            $bounds->getNorthEast()->getLatitude()
        );
        $this->assertEquals(
            8.5034583600970635,
            $bounds->getNorthEast()->getLongitude()
        );
    }

    public function testExpandWithBounds()
    {
        $math = new Math();

        $bounds = $math->expand(
            '-45 179 45 -179',
            '100 meters'
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

    public function testExpandThrowsForInvalidInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot cast to Bounds from input "foo".');

        $math = new Math();

        $math->expand(
            'foo',
            '100 meters'
        );
    }

    public function testShrinkExpandWithLatLng()
    {
        $math = new Math();

        $bounds = $math->shrink(
            '49.50042565 8.50207515',
            '100 meters'
        );

        $this->assertEquals(
            49.50042565,
            $bounds->getSouthWest()->getLatitude()
        );
        $this->assertEquals(
            8.50207515,
            $bounds->getSouthWest()->getLongitude()
        );
        $this->assertEquals(
            49.50042565,
            $bounds->getNorthEast()->getLatitude()
        );
        $this->assertEquals(
            8.50207515,
            $bounds->getNorthEast()->getLongitude()
        );
    }

    public function testShrinkWithBounds()
    {
        $math = new Math();

        $bounds = $math->shrink(
            '-45.000898315284132 178.99872959034192 45.000898315284132 -178.99872959034192',
            '100 meters'
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

    public function testShrinkThrowsForInvalidInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot cast to Bounds from input "foo".');

        $math = new Math();

        $math->shrink(
            'foo',
            '100 meters'
        );
    }
}
