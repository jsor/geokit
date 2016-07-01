<?php

namespace Geokit;

use Geokit\Fixtures\ThirdPartyLatLng;

class LngLatTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorShouldAcceptStringsAsArguments()
    {
        $LatLng = new LatLng('2.5678', '1.1234');

        $this->assertSame(1.1234, $LatLng->getLongitude());
        $this->assertSame(2.5678, $LatLng->getLatitude());
    }

    public function testConstructorShouldAcceptFloatsAsArguments()
    {
        $LatLng = new LatLng(2.5678, 1.1234);

        $this->assertSame(1.1234, $LatLng->getLongitude());
        $this->assertSame(2.5678, $LatLng->getLatitude());
    }

    public function testConstructorShouldNormalizeLatLng()
    {
        $LatLng = new LatLng(91, 181);

        $this->assertEquals(-179, $LatLng->getLongitude());
        $this->assertEquals(90, $LatLng->getLatitude());
    }

    public function testConstructorShouldAcceptLocalizedFloatsAsArguments()
    {
        $currentLocale = setlocale(LC_NUMERIC, '0');
        setlocale(LC_NUMERIC, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'deu_deu');

        $latitude = floatval('1.1234');
        $longitude = floatval('2.5678');

        $LatLng = new LatLng($latitude, $longitude);

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());

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

        $LatLng = new LatLng(2, 1);

        foreach ($keys as $key) {
            $this->assertTrue(isset($LatLng[$key]));
            $this->assertNotNull($LatLng[$key]);
        }
    }

    public function testOffsetGetThrowsExceptionForInvalidKey()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid offset "foo".');

        $LatLng = new LatLng(2, 1);

        $LatLng['foo'];
    }

    public function testOffsetSetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $LatLng = new LatLng(2, 1);

        $LatLng['lat'] = 5;
    }

    public function testOffsetUnsetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $LatLng = new LatLng(2, 1);

        unset($LatLng['lat']);
    }

    public function testToStringShouldReturnLatitudeAndLongitudeAsCommaSeparatedString()
    {
        $LatLng = new LatLng(2.5678, 1.1234);

        $this->assertSame(sprintf('%F,%F', 2.5678, 1.1234), (string) $LatLng);
    }

    public function testToStringShouldReturnLatitudeAndLongitudeAsCommaSeparatedStringWithLocalizedFloats()
    {
        $currentLocale = setlocale(LC_NUMERIC, '0');
        setlocale(LC_NUMERIC, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'deu_deu');

        $latitude = floatval('1.1234');
        $longitude = floatval('2.5678');

        $LatLng = new LatLng($latitude, $longitude);

        $this->assertSame(sprintf('%F,%F', 1.1234, 2.5678), (string) $LatLng);
        setlocale(LC_NUMERIC, $currentLocale);
    }

    public function testNormalizeShouldThrowExceptionIfInvalidDataSupplied()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize LatLng from input null.');
        LatLng::normalize(null);
    }

    public function testNormalizeShouldAcceptLatLngArgument()
    {
        $LatLng1 = new LatLng(2.5678, 1.1234);
        $LatLng2 = LatLng::normalize($LatLng1);

        $this->assertEquals($LatLng1, $LatLng2);
    }

    public function testNormalizeShouldAcceptStringArgument()
    {
        $LatLng = LatLng::normalize('1.1234,2.5678');

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayArgument()
    {
        $LatLng = LatLng::normalize(array('latitude' => 1.1234, 'longitude' => 2.5678));

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayArgumentWithShortKeys()
    {
        $LatLng = LatLng::normalize(array('lat' => 1.1234, 'lon' => 2.5678));

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());

        $LatLng = LatLng::normalize(array('lat' => 1.1234, 'lng' => 2.5678));

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayArgumentWithXYKeys()
    {
        $LatLng = LatLng::normalize(array('y' => 1.1234, 'x' => 2.5678));

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());
    }

    public function testNormalizeShouldAcceptArrayAccessArgument()
    {
        $LatLng = LatLng::normalize(new \ArrayObject(array('latitude' => 1.1234, 'longitude' => 2.5678)));

        $this->assertSame(1.1234, $LatLng->getLatitude());
        $this->assertSame(2.5678, $LatLng->getLongitude());
    }

    public function testNormalizeShouldAcceptIndexedArrayArgument()
    {
        $LatLng = LatLng::normalize(array(2.5678, 1.1234));

        $this->assertSame(2.5678, $LatLng->getLatitude());
        $this->assertSame(1.1234, $LatLng->getLongitude());
    }

    public function testNormalizeShouldAcceptObjectWithLatLngGetters()
    {
        $thirdPartyLatLng = new ThirdPartyLatLng(2.5678, 1.1234);

        $LatLng = LatLng::normalize($thirdPartyLatLng);

        $this->assertSame(2.5678, $LatLng->getLatitude());
        $this->assertSame(1.1234, $LatLng->getLongitude());
    }

    /**
     * @dataProvider normalizeLatDataProvider
     */
    public function testNormalizeLat($a, $b)
    {
        $this->assertEquals(LatLng::normalizeLat($a), $b);
    }

    public function normalizeLatDataProvider()
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
        $this->assertEquals(LatLng::normalizeLng($a), $b);
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
