<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests\Geocoding;

use Geokit\Geocoding\MultiGeocoder;
use Geokit\Geocoding\LocationFactory;
use Geokit\Geocoding\Response;
use Geokit\LatLng;
use Buzz\Browser;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geocoding\MultiGeocoder
 */
class MultiGeocoderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $geocoder = new MultiGeocoder();

        $response = $geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(404, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());

        $response = $geocoder->reverseGeocodeLatLng(new LatLng(1, 2));

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(404, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());

        $response = $geocoder->geocodeIp('123.456.789.000');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(404, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());
    }

    public function testConstructor()
    {
        $geocoder1 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $geocoder2 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();


        $geocoder = new MultiGeocoder(array($geocoder1, $geocoder2));

        $this->assertSame(array($geocoder1, $geocoder2), $geocoder->all());
    }

    public function testGeocodeAddress()
    {
        $geocoder1 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $response1 = new Response(404, $geocoder1);
        $geocoder1
            ->expects($this->once())
            ->method('geocodeAddress')
            ->with('742 Evergreen Terrace')
            ->will($this->returnValue($response1));

        $geocoder2 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $response2 = new Response(200, $geocoder2);
        $geocoder2
            ->expects($this->once())
            ->method('geocodeAddress')
            ->with('742 Evergreen Terrace')
            ->will($this->returnValue($response2));

        $geocoder = new MultiGeocoder(array($geocoder1, $geocoder2));

        $this->assertSame($response2, $geocoder->geocodeAddress('742 Evergreen Terrace'));
    }

    public function testReverseGeocodeLatLng()
    {
        $latLng = new LatLng(1, 2);

        $geocoder1 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $response1 = new Response(404, $geocoder1);
        $geocoder1
            ->expects($this->once())
            ->method('reverseGeocodeLatLng')
            ->with($latLng)
            ->will($this->returnValue($response1));

        $geocoder2 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $response2 = new Response(200, $geocoder2);
        $geocoder2
            ->expects($this->once())
            ->method('reverseGeocodeLatLng')
            ->with($latLng)
            ->will($this->returnValue($response2));

        $geocoder = new MultiGeocoder(array($geocoder1, $geocoder2));

        $this->assertSame($response2, $geocoder->reverseGeocodeLatLng($latLng));
    }

    public function testGeocodeIp()
    {
        $geocoder1 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $response1 = new Response(404, $geocoder1);
        $geocoder1
            ->expects($this->once())
            ->method('geocodeIp')
            ->with('123.456.789.000')
            ->will($this->returnValue($response1));

        $geocoder2 = $this->getMockBuilder('Geokit\Geocoding\GeocoderInterface')
                          ->getMock();

        $response2 = new Response(200, $geocoder2);
        $geocoder2
            ->expects($this->once())
            ->method('geocodeIp')
            ->with('123.456.789.000')
            ->will($this->returnValue($response2));

        $geocoder = new MultiGeocoder(array($geocoder1, $geocoder2));

        $this->assertSame($response2, $geocoder->geocodeIp('123.456.789.000'));
    }
}
