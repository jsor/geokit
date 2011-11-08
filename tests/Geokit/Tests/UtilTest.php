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

use Geokit\Util;
use Geokit\LatLng;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Util
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testDistanceDataProvider
     */
    public function testDistance($latLng1, $latLng2, $distance)
    {
        $this->assertEquals(sprintf('%F', $distance), sprintf('%F', Util::distance($latLng1, $latLng2)));
    }

    public static function testDistanceDataProvider()
    {
        return array(
            array(
                new LatLng(44.65105198323727, 60.463472083210945),
                new LatLng(-35.21140778437257, 83.73959356918931),
                9195565.498553
            ),
            array(
                new LatLng(85.67559066228569, -100.69272816181183),
                new LatLng(8.659202512353659, -169.56520546227694),
                8883859.066222
            ),
            array(
                new LatLng(-61.20406142435968, 86.67973218485713),
                new LatLng(-46.86954100616276, -112.75070607662201),
                7880555.941837
            ),
            array(
                new LatLng(19.441748214885592, 114.92620809003711),
                new LatLng(82.39083864726126, 5.652987342327833),
                8150775.281327
            ),
            array(
                new LatLng(-15.120142288506031, 71.53828611597419),
                new LatLng(-28.01164012402296, 176.72984121367335),
                10662979.213296
            ),
            array(
                new LatLng(-30.777964973822236, 69.48629681020975),
                new LatLng(8.096220837906003, -13.63121923059225),
                9828259.154707
            ),
            array(
                new LatLng(-69.95015325956047, -144.45135304704309),
                new LatLng(23.054808229207993, -115.67441381514072),
                10602405.644377
            ),
            array(
                new LatLng(-69.93624382652342, -36.89967077225447),
                new LatLng(53.617225270718336, 129.4124977849424),
                18093990.701601
            ),
            array(
                new LatLng(-42.07365347072482, 176.21298506855965),
                new LatLng(-54.178582448512316, 81.13013628870249),
                6643414.630363
            ),
            array(
                new LatLng(-62.89867605082691, 3.9036516286432743),
                new LatLng(44.33718089014292, -49.99945605173707),
                12855027.218898
            ),
            array(
                new LatLng(-50.42842207476497, 139.02099810540676),
                new LatLng(62.958827102556825, -141.98338044807315),
                14376298.755608
            ),
            array(
                new LatLng(62.66255331225693, -20.930572282522917),
                new LatLng(-30.06355374120176, -36.294518653303385),
                10412950.900360
            ),
            array(
                new LatLng(-80.98694069311023, 24.97568277642131),
                new LatLng(3.151013720780611, 78.1766682676971),
                9767326.075276
            ),
            array(
                new LatLng(-42.36720213666558, 28.86812360957265),
                new LatLng(-46.722429636865854, 152.25086364895105),
                8656764.244774
            ),
            array(
                new LatLng(0.13397407718002796, -176.59395882859826),
                new LatLng(-87.33300279825926, -171.5560189448297),
                9737923.923407
            ),
            array(
                new LatLng(-51.00516985170543, -167.77878485620022),
                new LatLng(10.981508698314428, 132.4349656328559),
                8975706.158452
            ),
            array(
                new LatLng(-22.765081711113453, 134.544414896518),
                new LatLng(63.991813687607646, 20.827705543488264),
                13435190.814963
            ),
            array(
                new LatLng(65.62432050704956, 175.91195974498987),
                new LatLng(-63.84489024057984, -122.14013747870922),
                15257140.280495
            ),
            array(
                new LatLng(-44.645075937733054, 140.418138820678),
                new LatLng(-76.16916658356786, -30.36532362923026),
                6572207.1047724
            ),
            array(
                new LatLng(65.00346723943949, -173.44978725537658),
                new LatLng(54.71282500773668, -123.0979248136282),
                2940926.1954457
            )
          );
    }

    /**
     * @dataProvider testDistanceDataProvider
     */
    public function testHeading()
    {
        $this->assertEquals(90, Util::heading(new LatLng(0, 0), new LatLng(0, 1)));
        $this->assertEquals(0,  Util::heading(new LatLng(0, 0), new LatLng(1, 0)));
        $this->assertEquals(270, Util::heading(new LatLng(0, 0), new LatLng(0, -1)));
        $this->assertEquals(180, Util::heading(new LatLng(0, 0), new LatLng(-1, 0)));
    }

    public function testNormalizeLatLngShouldThrowExceptionIfInvalidDataSupplied()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot create LatLng');
        Util::normalizeLatLng(null);
    }

    public function testNormalizeLatLngShouldAcceptLatLngArgument()
    {
        $latLng1 = new LatLng(1.1234, 2.5678);
        $latLng2 = Util::normalizeLatLng($latLng1);

        $this->assertEquals($latLng1, $latLng2);
    }

    public function testNormalizeLatLngShouldAcceptPointArgument()
    {
        $point = new Point(2.5678, 1.1234);
        $latLng2 = Util::normalizeLatLng($point);

        $this->assertEquals(new LatLng(1.1234, 2.5678), $latLng2);
    }

    public function testNormalizeLatLngShouldAcceptStringArgument()
    {
        $latLng = Util::normalizeLatLng('1.1234,2.5678');

        $this->assertSame(1.1234, $latLng->getLatitude());
        $this->assertSame(2.5678, $latLng->getLongitude());
    }

    public function testNormalizeLatLngShouldAcceptArrayArgument()
    {
        $latLng = Util::normalizeLatLng(array('latitude' => 1.1234, 'longitude' => 2.5678));

        $this->assertSame(1.1234, $latLng->getLatitude());
        $this->assertSame(2.5678, $latLng->getLongitude());
    }

    public function testNormalizeLatLngShouldAcceptArrayArgumentWithShortKeys()
    {
        $latLng = Util::normalizeLatLng(array('lat' => 1.1234, 'lon' => 2.5678));

        $this->assertSame(1.1234, $latLng->getLatitude());
        $this->assertSame(2.5678, $latLng->getLongitude());

        $latLng = Util::normalizeLatLng(array('lat' => 1.1234, 'lng' => 2.5678));

        $this->assertSame(1.1234, $latLng->getLatitude());
        $this->assertSame(2.5678, $latLng->getLongitude());
    }

    public function testNormalizeLatLngShouldAcceptArrayAccessArgument()
    {
        $latLng = Util::normalizeLatLng(new \ArrayObject(array('latitude' => 1.1234, 'longitude' => 2.5678)));

        $this->assertSame(1.1234, $latLng->getLatitude());
        $this->assertSame(2.5678, $latLng->getLongitude());
    }

    /**
     * @dataProvider testNormalizeLatDataProvider
     */
    public function testNormalizeLat($a, $b)
    {
        $this->assertEquals(Util::normalizeLat($a), $b);
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
        $this->assertEquals(Util::normalizeLng($a), $b);
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
