<?php

namespace Geokit;

class LngLatTest extends TestCase
{
    public function testConstructorShouldAcceptStringsAsArguments()
    {
        $LatLng = new LatLng('2.5678', '1.1234');

        $this->assertSame(1.1234, $LatLng->longitude());
        $this->assertSame(2.5678, $LatLng->latitude());
    }

    public function testConstructorShouldAcceptFloatsAsArguments()
    {
        $LatLng = new LatLng(2.5678, 1.1234);

        $this->assertSame(1.1234, $LatLng->longitude());
        $this->assertSame(2.5678, $LatLng->latitude());
    }

    public function testConstructorShouldNormalizeLatLng()
    {
        $LatLng = new LatLng(91, 181);

        $this->assertEquals(-179, $LatLng->longitude());
        $this->assertEquals(90, $LatLng->latitude());
    }

    public function testConstructorShouldAcceptLocalizedFloatsAsArguments()
    {
        $currentLocale = \setlocale(\LC_NUMERIC, '0');
        \setlocale(\LC_NUMERIC, 'de_DE.utf8', 'de_DE@euro', 'de_DE', 'deu_deu');

        $latitude = \floatval('1.1234');
        $longitude = \floatval('2.5678');

        $LatLng = new LatLng($latitude, $longitude);

        $this->assertSame(1.1234, $LatLng->latitude());
        $this->assertSame(2.5678, $LatLng->longitude());

        \setlocale(\LC_NUMERIC, $currentLocale);
    }
}
