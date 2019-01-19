<?php

namespace Geokit;

use Geokit\Fixtures\ThirdPartyLatLng;

class LngLatTest extends TestCase
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
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot normalize LatLng from input null.');
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
}
