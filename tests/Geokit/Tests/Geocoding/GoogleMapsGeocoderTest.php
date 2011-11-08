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

use Geokit\Geocoding\GoogleMapsGeocoder;
use Geokit\Geocoding\LocationFactory;
use Geokit\Geocoding\LocationInterface;
use Geokit\Geocoding\Response;
use Geokit\LatLng;
use Geokit\Bounds;
use Buzz\Browser;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geocoding\GoogleMapsGeocoder
 */
class GoogleMapsGeocoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Geokit\Geocoding\GoogleMapsGeocoder
     */
    private $geocoder;

    public function setUp()
    {
        parent::setUp();

        $client = new \Buzz\Client\Mock\FIFO();
        $browser = new Browser($client);

        $this->geocoder = new GoogleMapsGeocoder(new LocationFactory(), $browser);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->geocoder = null;
    }

    public function testCustomUrlAndParameters()
    {
        $browser = $this->getMockBuilder('\Buzz\Browser')
                        ->getMock();

        $response = new \Buzz\Message\Response();
        $response->addHeader('HTTP/1.0 404 Not Found');

        $browser->expects($this->once())
                ->method('get')
                ->with('http://example.com?address=742+Evergreen+Terrace&language=de&foo=bar&sensor=false')
                ->will($this->returnValue($response));

        $geocoder = new GoogleMapsGeocoder(null, $browser);

        $geocoder->setApiUri('http://example.com');
        $geocoder->setRequestParams(array('language' => 'de', 'foo' => 'bar'));

        $geocoder->geocodeAddress('742 Evergreen Terrace');
    }

    public function testGeocodeAddressZeroResultsResponse()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString('HTTP/1.0 200 OK
Content-Type: application/json; charset=UTF-8

{"status": "ZERO_RESULTS", "results": []}');

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(404, $response->getCode());
        $this->assertNull($response->getLocation());
    }

    public function testGeocodeAddressInvalidRequestResponse()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString('HTTP/1.0 200 OK
Content-Type: application/json; charset=UTF-8

{"status": "INVALID_REQUEST", "results": []}');

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(400, $response->getCode());
        $this->assertNull($response->getLocation());
    }

    public function testGeocodeAddressRequestDeniedResponse()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString('HTTP/1.0 200 OK
Content-Type: application/json; charset=UTF-8

{"status": "REQUEST_DENIED", "results": []}');

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(403, $response->getCode());
        $this->assertNull($response->getLocation());
    }

    public function testGeocodeAddressOverQueryLimitResponse()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString('HTTP/1.0 200 OK
Content-Type: application/json; charset=UTF-8

{"status": "REQUEST_DENIED", "results": []}');

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(403, $response->getCode());
        $this->assertNull($response->getLocation());
    }

    public function testGeocodeAddress()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString(file_get_contents(__DIR__.'/Fixtures/googlemapsgeocoder_response_1.txt'));

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('1600 Amphitheatre Parkway, Mountain View, CA');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertSame($this->geocoder, $response->getGeocoder());

        $location = $response->getLocation();

        $this->assertInstanceOf('\Geokit\Geocoding\LocationInterface', $location);
        $this->assertEquals(new LatLng(37.4219720, -122.0841430), $location->getLatLng());
        $this->assertEquals(LocationInterface::ACCURACY_PRECISE, $location->getAccuracy());
        $this->assertEquals(new Bounds(new LatLng(37.4188244, -122.0872906), new LatLng(37.4251196, -122.0809954)), $location->getBounds());
        $this->assertEquals(new Bounds(new LatLng(37.4188244, -122.0872906), new LatLng(37.4251196, -122.0809954)), $location->getViewport());
        $this->assertEquals('1600 Amphitheatre Pkwy, Mountain View, CA 94043, USA', $location->getFormattedAddress());
        $this->assertEquals('1600', $location->getStreetNumber());
        $this->assertEquals('Amphitheatre Pkwy', $location->getStreetName());
        $this->assertEquals('94043', $location->getPostalCode());
        $this->assertEquals('Mountain View', $location->getLocality());
        $this->assertEquals('California', $location->getRegion());
        $this->assertEquals('United States', $location->getCountryName());
        $this->assertEquals('US', $location->getCountryCode());
    }

    public function testReverseGeocodeLatLng()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString(file_get_contents(__DIR__.'/Fixtures/googlemapsgeocoder_response_2.txt'));

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->reverseGeocodeLatLng(new LatLng(40.714224, -73.961452));

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertSame($this->geocoder, $response->getGeocoder());

        $location = $response->getLocation();

        $this->assertInstanceOf('\Geokit\Geocoding\LocationInterface', $location);

        $this->assertEquals(new LatLng(40.71412890, -73.96140740), $location->getLatLng());
        $this->assertEquals(LocationInterface::ACCURACY_PRECISE, $location->getAccuracy());
        $this->assertEquals(new Bounds(new LatLng(40.71277991970850, -73.96275638029151), new LatLng(40.71547788029149, -73.96005841970849)), $location->getViewport());
        $this->assertEquals('285 Bedford Ave, Brooklyn, NY 11211, USA', $location->getFormattedAddress());
        $this->assertEquals('285', $location->getStreetNumber());
        $this->assertEquals('Bedford Ave', $location->getStreetName());
        $this->assertEquals('11211', $location->getPostalCode());
        $this->assertEquals('New York', $location->getLocality());
        $this->assertEquals('New York', $location->getRegion());
        $this->assertEquals('United States', $location->getCountryName());
        $this->assertEquals('US', $location->getCountryCode());

        $this->assertCount(10, $response->getAdditionalLocations());
    }
}
