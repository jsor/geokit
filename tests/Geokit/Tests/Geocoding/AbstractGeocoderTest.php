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

use Geokit\Geocoding\LocationFactory;
use Geokit\Geocoding\Response;
use Geokit\LatLng;
use Buzz\Browser;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geocoding\AbstractGeocoder
 */
class AbstractGeocoderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorCreatesDefaultLocationFactory()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');
        $this->assertInstanceOf('\Geokit\Geocoding\LocationFactory', $geocoder->getLocationFactory());
    }

    public function testConstructorCreatesDefaultBrowser()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');
        $this->assertInstanceOf('\Buzz\Browser', $geocoder->getBrowser());
    }

    public function testSetLocationFactory()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');

        $factory = new LocationFactory();
        $return = $geocoder->setLocationFactory($factory);

        $this->assertSame($factory, $geocoder->getLocationFactory());
        $this->assertSame($return, $geocoder);
    }

    public function testSetBrowser()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');

        $browser = new Browser();
        $return = $geocoder->setBrowser($browser);

        $this->assertSame($browser, $geocoder->getBrowser());
        $this->assertSame($return, $geocoder);
    }

    public function testGeocodeAddressReturnsNotImplementedResponse()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');

        $response = $geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(501, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());
    }

    public function testReverseGeocodeLatLngReturnsNotImplementedResponse()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');

        $response = $geocoder->reverseGeocodeLatLng(new LatLng(1, 2));

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(501, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());
    }

    public function testGeocodeIpReturnsNotImplementedResponse()
    {
        $geocoder = $this->getMockForAbstractClass('\Geokit\Geocoding\AbstractGeocoder');

        $response = $geocoder->geocodeIp('123.456.789.000');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(501, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());
    }
}
