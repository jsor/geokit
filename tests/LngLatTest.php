<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit;

/**
 * @covers Geokit\LngLat
 */
class LngLatTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorShouldAcceptStringsAsArguments()
    {
        $LngLat = new LngLat('1.1234', '2.5678');

        $this->assertSame(1.1234, $LngLat->getLongitude());
        $this->assertSame(2.5678, $LngLat->getLatitude());
    }

    public function testConstructorShouldAcceptFloatsAsArguments()
    {
        $LngLat = new LngLat(1.1234, 2.5678);

        $this->assertSame(1.1234, $LngLat->getLongitude());
        $this->assertSame(2.5678, $LngLat->getLatitude());
    }

    public function testConstructorShouldNormalizeLngLat()
    {
        $LngLat = new LngLat(181, 91);

        $this->assertEquals(-179, $LngLat->getLongitude());
        $this->assertEquals(90, $LngLat->getLatitude());
    }

    public function testConstructorShouldAcceptLocalizedFloatsAsArguments()
    {
        $currentLocale = setlocale(LC_NUMERIC, '0');
        setlocale(LC_NUMERIC, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'deu_deu');

        $latitude  = floatval('1.1234');
        $longitude = floatval('2.5678');

        $LngLat = new LngLat($longitude, $latitude);

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());

        setlocale(LC_NUMERIC, $currentLocale);
    }

    public function testArrayAccess()
    {
        $keys = array(
            'latitude',
            'lat',
            'y',
            'longitude',
            'lng',
            'lon',
            'x'
        );

        $LngLat = new LngLat(1, 2);

        foreach ($keys as $key) {
            $this->assertTrue(isset($LngLat[$key]));
            $this->assertNotNull($LngLat[$key]);
        }
    }

    public function testOffsetGetThrowsExceptionForInvalidKey()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid offset "foo".');

        $LngLat = new LngLat(1, 2);

        $LngLat['foo'];
    }

    public function testOffsetSetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $LngLat = new LngLat(1, 2);

        $LngLat['lat'] = 5;
    }

    public function testOffsetUnsetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $LngLat = new LngLat(1, 2);

        unset($LngLat['lat']);
    }

    public function testToStringShouldReturnLatitudeAndLongitudeAsCommaSeparatedString()
    {
        $LngLat = new LngLat(1.1234, 2.5678);

        $this->assertSame(sprintf('%F,%F', 1.1234, 2.5678), (string) $LngLat);
    }

    public function testToStringShouldReturnLatitudeAndLongitudeAsCommaSeparatedStringWithLocalizedFloats()
    {
        $currentLocale = setlocale(LC_NUMERIC, '0');
        setlocale(LC_NUMERIC, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'deu_deu');

        $latitude  = floatval('1.1234');
        $longitude = floatval('2.5678');

        $LngLat = new LngLat($latitude, $longitude);

        $this->assertSame(sprintf('%F,%F', 1.1234, 2.5678), (string) $LngLat);
        setlocale(LC_NUMERIC, $currentLocale);
    }

    public function testDistanceTo()
    {
        $LngLat = new LngLat(60.463472083210945, 44.65105198323727);

        $distance = $LngLat->distanceTo(new LngLat(83.73959356918931, -35.21140778437257));

        $this->assertEquals(sprintf('%F', 9195568.382018), sprintf('%F', $distance->meters()));
    }

    public function testHeading()
    {
        $LngLat = new LngLat(0, 0);

        $this->assertEquals(90, $LngLat->headingTo(new LngLat(1, 0)));
        $this->assertEquals(0,  $LngLat->headingTo(new LngLat(0, 1)));
        $this->assertEquals(270, $LngLat->headingTo(new LngLat(-1, 0)));
        $this->assertEquals(180, $LngLat->headingTo(new LngLat(0, -1)));
    }

    public function testNormalizeShouldThrowExceptionIfInvalidDataSupplied()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize LngLat from input null.');
        LngLat::normalize(null);
    }

    public function testNormalizeShouldAcceptLngLatArgument()
    {
        $LngLat1 = new LngLat(1.1234, 2.5678);
        $LngLat2 = LngLat::normalize($LngLat1);

        $this->assertEquals($LngLat1, $LngLat2);
    }

    public function testNormalizeShouldAcceptStringArgument()
    {
        $LngLat = LngLat::normalize('1.1234,2.5678');

        $this->assertSame(1.1234, $LngLat->getLongitude());
        $this->assertSame(2.5678, $LngLat->getLatitude());
    }

    public function testNormalizeShouldAcceptArrayArgument()
    {
        $LngLat = LngLat::normalize(array('latitude' => 1.1234, 'longitude' => 2.5678));

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayArgumentWithShortKeys()
    {
        $LngLat = LngLat::normalize(array('lat' => 1.1234, 'lon' => 2.5678));

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());

        $LngLat = LngLat::normalize(array('lat' => 1.1234, 'lng' => 2.5678));

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayArgumentWithXYKeys()
    {
        $LngLat = LngLat::normalize(array('y' => 1.1234, 'x' => 2.5678));

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayAccessArgument()
    {
        $LngLat = LngLat::normalize(new \ArrayObject(array('latitude' => 1.1234, 'longitude' => 2.5678)));

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());
    }

    public function testNormalizeShouldAcceptIndexedArrayArgument()
    {
        $LngLat = LngLat::normalize(array(2.5678, 1.1234));

        $this->assertSame(1.1234, $LngLat->getLatitude());
        $this->assertSame(2.5678, $LngLat->getLongitude());
    }

    /**
     * @dataProvider testNormalizeLatDataProvider
     */
    public function testNormalizeLat($a, $b)
    {
        $this->assertEquals(LngLat::normalizeLat($a), $b);
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
        $this->assertEquals(LngLat::normalizeLng($a), $b);
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
