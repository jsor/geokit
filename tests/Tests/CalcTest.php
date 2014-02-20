<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests;

use Geokit\Calc;

/**
 * @covers Geokit\Calc
 */
class CalcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testDistanceHaversineDataProvider
     */
    public function testDistanceHaversine($latLng1, $latLng2, $distance)
    {
        $this->assertEquals(
            sprintf('%F', $distance),
            sprintf('%F', Calc::distanceHaversine($latLng1[0], $latLng1[1], $latLng2[0], $latLng2[1])->meters())
        );
    }

    public static function testDistanceHaversineDataProvider()
    {
        return array(
            array(
                array(44.65105198323727, 60.463472083210945),
                array(-35.21140778437257, 83.73959356918931),
                9195568.382018
            ),
            array(
                array(85.67559066228569, -100.69272816181183),
                array(8.659202512353659, -169.56520546227694),
                8883861.851945
            ),
            array(
                array(-61.20406142435968, 86.67973218485713),
                array(-46.86954100616276, -112.75070607662201),
                7880558.412953
            ),
            array(
                array(19.441748214885592, 114.92620809003711),
                array(82.39083864726126, 5.652987342327833),
                8150777.837176
            ),
            array(
                array(-15.120142288506031, 71.53828611597419),
                array(-28.01164012402296, 176.72984121367335),
                10662982.556900
            ),
            array(
                array(-30.777964973822236, 69.48629681020975),
                array(8.096220837906003, -13.63121923059225),
                9828262.236567
            ),
            array(
                array(-69.95015325956047, -144.45135304704309),
                array(23.054808229207993, -115.67441381514072),
                10602408.968987
            ),
            array(
                array(-69.93624382652342, -36.89967077225447),
                array(53.617225270718336, 129.4124977849424),
                18093996.375357
            ),
            array(
                array(-42.07365347072482, 176.21298506855965),
                array(-54.178582448512316, 81.13013628870249),
                6643416.713547
            ),
            array(
                array(-62.89867605082691, 3.9036516286432743),
                array(44.33718089014292, -49.99945605173707),
                12855031.249865
            ),
            array(
                array(-50.42842207476497, 139.02099810540676),
                array(62.958827102556825, -141.98338044807315),
                14376303.263602
            ),
            array(
                array(62.66255331225693, -20.930572282522917),
                array(-30.06355374120176, -36.294518653303385),
                10412954.165562
            ),
            array(
                array(-80.98694069311023, 24.97568277642131),
                array(3.151013720780611, 78.1766682676971),
                9767329.138029
            ),
            array(
                array(-42.36720213666558, 28.86812360957265),
                array(-46.722429636865854, 152.25086364895105),
                8656766.959287
            ),
            array(
                array(0.13397407718002796, -176.59395882859826),
                array(-87.33300279825926, -171.5560189448297),
                9737926.976940
            ),
            array(
                array(-51.00516985170543, -167.77878485620022),
                array(10.981508698314428, 132.4349656328559),
                8975708.972975
            ),
            array(
                array(-22.765081711113453, 134.544414896518),
                array(63.991813687607646, 20.827705543488264),
                13435195.027854
            ),
            array(
                array(65.62432050704956, 175.91195974498987),
                array(-63.84489024057984, -122.14013747870922),
                15257145.064696
            ),
            array(
                array(-44.645075937733054, 140.418138820678),
                array(-76.16916658356786, -30.36532362923026),
                6572209.165628
            ),
            array(
                array(65.00346723943949, -173.44978725537658),
                array(54.71282500773668, -123.0979248136282),
                2940927.117636
            )
          );
    }

    /**
     * @dataProvider testDistanceVincentyDataProvider
     */
    public function testDistanceVincenty($latLng1, $latLng2, $distance)
    {
        $this->assertEquals(
            sprintf('%F', $distance),
            sprintf('%F', Calc::distanceVincenty($latLng1[0], $latLng1[1], $latLng2[0], $latLng2[1])->meters())
        );
    }

    public static function testDistanceVincentyDataProvider()
    {
        return array(
            array(
                array(44.65105198323727, 60.463472083210945),
                array(-35.21140778437257, 83.73959356918931),
                9151350.584115
            ),
            array(
                array(85.67559066228569, -100.69272816181183),
                array(8.659202512353659, -169.56520546227694),
                8872957.883065
            ),
            array(
                array(-61.20406142435968, 86.67973218485713),
                array(-46.86954100616276, -112.75070607662201),
                7896462.224523
            ),
            array(
                array(19.441748214885592, 114.92620809003711),
                array(82.39083864726126, 5.652987342327833),
                8148846.707182
            ),
            array(
                array(-15.120142288506031, 71.53828611597419),
                array(-28.01164012402296, 176.72984121367335),
                10666157.523016
            ),
            array(
                array(-30.777964973822236, 69.48629681020975),
                array(8.096220837906003, -13.63121923059225),
                9818690.274757
            ),
            array(
                array(-69.95015325956047, -144.45135304704309),
                array(23.054808229207993, -115.67441381514072),
                10564410.159096
            ),
            array(
                array(-69.93624382652342, -36.89967077225447),
                array(53.617225270718336, 129.4124977849424),
                18058852.199871
            ),
            array(
                array(-42.07365347072482, 176.21298506855965),
                array(-54.178582448512316, 81.13013628870249),
                6654712.650668
            ),
            array(
                array(-62.89867605082691, 3.9036516286432743),
                array(44.33718089014292, -49.99945605173707),
                12810693.042278
            ),
            array(
                array(-50.42842207476497, 139.02099810540676),
                array(62.958827102556825, -141.98338044807315),
                14334895.461358
            ),
            array(
                array(62.66255331225693, -20.930572282522917),
                array(-30.06355374120176, -36.294518653303385),
                10369161.033962
            ),
            array(
                array(-80.98694069311023, 24.97568277642131),
                array(3.151013720780611, 78.1766682676971),
                9746495.212962
            ),
            array(
                array(-42.36720213666558, 28.86812360957265),
                array(-46.722429636865854, 152.25086364895105),
                8670341.181668
            ),
            array(
                array(0.13397407718002796, -176.59395882859826),
                array(-87.33300279825926, -171.5560189448297),
                9720046.244631
            ),
            array(
                array(-51.00516985170543, -167.77878485620022),
                array(10.981508698314428, 132.4349656328559),
                8952091.427134
            ),
            array(
                array(-22.765081711113453, 134.544414896518),
                array(63.991813687607646, 20.827705543488264),
                13409176.736188
            ),
            array(
                array(65.62432050704956, 175.91195974498987),
                array(-63.84489024057984, -122.14013747870922),
                15212358.215264
            ),
            array(
                array(-44.645075937733054, 140.418138820678),
                array(-76.16916658356786, -30.36532362923026),
                6584621.671694
            ),
            array(
                array(65.00346723943949, -173.44978725537658),
                array(54.71282500773668, -123.0979248136282),
                2947504.722066
            )
          );
    }

    public function testHeading()
    {
        $this->assertEquals(90,  Calc::heading(0, 0, 0, 1));
        $this->assertEquals(0,   Calc::heading(0, 0, 1, 0));
        $this->assertEquals(270, Calc::heading(0, 0, 0, -1));
        $this->assertEquals(180, Calc::heading(0, 0, -1, 0));
    }

    /**
     * @dataProvider testNormalizeLatDataProvider
     */
    public function testNormalizeLat($a, $b)
    {
        $this->assertEquals(Calc::normalizeLat($a), $b);
    }

    public function testNormalizeLatDataProvider()
    {
        return array(
            array(-95, -90),
            array(-90, -90),
            array(5, 5),
            array(90, 90),
            array(180, 90)
        );
    }

    /**
     * @dataProvider testNormalizeLngDataProvider
     */
    public function testNormalizeLng($a, $b)
    {
        $this->assertEquals(Calc::normalizeLng($a), $b);
    }

    public function testNormalizeLngDataProvider()
    {
        return array(
            array(-545, 175),
            array(-365, -5),
            array(-185, 175),
            array(-180, -180),
            array(5, 5),
            array(180, 180),
            array(215, -145),
            array(360, 0),
            array(395, 35),
            array(540, 180)
        );
    }
}
