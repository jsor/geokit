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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Util
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
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
