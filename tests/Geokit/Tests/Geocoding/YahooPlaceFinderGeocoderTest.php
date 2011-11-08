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

use Geokit\Geocoding\YahooPlaceFinderGeocoder;
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
 * @covers Geokit\Geocoding\YahooPlaceFinderGeocoder
 */
class YahooPlaceFinderGeocoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Geokit\Geocoding\YahooPlaceFinderGeocoder
     */
    private $geocoder;

    public function setUp()
    {
        parent::setUp();

        $client = new \Buzz\Client\Mock\FIFO();
        $browser = new Browser($client);

        $this->geocoder = new YahooPlaceFinderGeocoder('appId', new LocationFactory(), $browser);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->geocoder = null;
    }

    public function testEmptyAppIdThrowsException()
    {
        $this->setExpectedException('\InvalidArgumentException', 'The application id is empty');
        new YahooPlaceFinderGeocoder(null);
    }

    public function testCustomUrlAndParameters()
    {
        $browser = $this->getMockBuilder('\Buzz\Browser')
                        ->getMock();

        $response = new \Buzz\Message\Response();
        $response->addHeader('HTTP/1.0 404 Not Found');

        $browser->expects($this->once())
                ->method('get')
                ->with('http://example.com?q=742+Evergreen+Terrace&locale=de_DE&foo=bar&appid=appId&flags=JX')
                ->will($this->returnValue($response));

        $geocoder = new YahooPlaceFinderGeocoder('appId', null, $browser);

        $geocoder->setApiUri('http://example.com');
        $geocoder->setRequestParams(array('locale' => 'de_DE', 'foo' => 'bar'));

        $geocoder->geocodeAddress('742 Evergreen Terrace');
    }

    public function testJFlagAutomaticallyAdded()
    {
        $browser = $this->getMockBuilder('\Buzz\Browser')
                        ->getMock();

        $response = new \Buzz\Message\Response();
        $response->addHeader('HTTP/1.0 404 Not Found');

        $browser->expects($this->once())
                ->method('get')
                ->with('http://example.com?q=742+Evergreen+Terrace&flags=XJ&appid=appId&locale=en_US')
                ->will($this->returnValue($response));

        $geocoder = new YahooPlaceFinderGeocoder('appId', null, $browser);

        $geocoder->setApiUri('http://example.com');
        $geocoder->setRequestParams(array('flags' => 'X'));

        $geocoder->geocodeAddress('742 Evergreen Terrace');
    }

    public function testGeocodeAddressZeroResultsResponse()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString('HTTP/1.0 200 OK
Content-Type: application/json; charset=UTF-8

{"ResultSet":{"version":"1.0","Error":0,"ErrorMessage":"No error","Locale":"us_US","Quality":10,"Found":0}}');

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

{"ResultSet":{"version":"1.0","Error":100,"ErrorMessage":"No location parameters","Locale":"us_US","Quality":0,"Found":0}}');

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('742 Evergreen Terrace');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(400, $response->getCode());
        $this->assertNull($response->getLocation());
    }

    public function testGeocodeAddress()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString(file_get_contents(__DIR__.'/Fixtures/yahooplacefindergeocoder_response_1.txt'));

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->geocodeAddress('1600 Amphitheatre Parkway, Mountain View, CA');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertSame($this->geocoder, $response->getGeocoder());

        $location = $response->getLocation();

        $this->assertInstanceOf('\Geokit\Geocoding\LocationInterface', $location);
        $this->assertEquals(new LatLng(37.423232, -122.085569), $location->getLatLng());
        $this->assertEquals(LocationInterface::ACCURACY_PRECISE, $location->getAccuracy());
        $this->assertEquals(new Bounds(new LatLng(37.423232, -122.085569), new LatLng(37.423232, -122.085569)), $location->getBounds());
        $this->assertEquals(null, $location->getViewport());
        $this->assertEquals(null, $location->getFormattedAddress());
        $this->assertEquals('1600', $location->getStreetNumber());
        $this->assertEquals('Amphitheatre Pky', $location->getStreetName());
        $this->assertEquals('94043-1351', $location->getPostalCode());
        $this->assertEquals('Mountain View', $location->getLocality());
        $this->assertEquals('California', $location->getRegion());
        $this->assertEquals('United States', $location->getCountryName());
        $this->assertEquals('US', $location->getCountryCode());
    }

    public function testReverseGeocodeLatLng()
    {
        $response = new \Buzz\Message\Response();
        $response->fromString(file_get_contents(__DIR__.'/Fixtures/yahooplacefindergeocoder_response_2.txt'));

        $this->geocoder->getBrowser()->getClient()->sendToQueue($response);
        $response = $this->geocoder->reverseGeocodeLatLng(new LatLng(40.714224, -73.961452));

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertSame($this->geocoder, $response->getGeocoder());

        $location = $response->getLocation();

        $this->assertInstanceOf('\Geokit\Geocoding\LocationInterface', $location);

        $this->assertEquals(new LatLng(40.714129, -73.961407), $location->getLatLng());
        $this->assertEquals(LocationInterface::ACCURACY_PRECISE, $location->getAccuracy());
        $this->assertEquals(new Bounds(new LatLng(40.714129, -73.961407), new LatLng(40.714129, -73.961407)), $location->getBounds());
        $this->assertEquals(null, $location->getViewport());
        $this->assertEquals(null, $location->getFormattedAddress());
        $this->assertEquals('285', $location->getStreetNumber());
        $this->assertEquals('Bedford Ave', $location->getStreetName());
        $this->assertEquals('11211-4203', $location->getPostalCode());
        $this->assertEquals('Brooklyn', $location->getLocality());
        $this->assertEquals('New York', $location->getRegion());
        $this->assertEquals('United States', $location->getCountryName());
        $this->assertEquals('US', $location->getCountryCode());

        $this->assertCount(0, $response->getAdditionalLocations());
    }
}
