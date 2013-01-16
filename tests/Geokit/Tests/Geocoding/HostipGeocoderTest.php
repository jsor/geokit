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

use Geokit\Geocoding\HostipGeocoder;
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
 * @covers Geokit\Geocoding\HostipGeocoder
 */
class HostipGeocoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * @var \Geokit\Geocoding\HostipGeocoder
     */
    private $geocoder;

    public function setUp()
    {
        parent::setUp();

        $this->browser = $this->getMockBuilder('\Buzz\Browser')
                               ->getMock();

        $this->geocoder = new HostipGeocoder(new LocationFactory(), $this->browser);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->geocoder = null;
    }

    public function testCustomUrl()
    {
        $response = new \Buzz\Message\Response();
        $response->addHeader('HTTP/1.0 404 Not Found');

        $this->browser->expects($this->once())
                      ->method('get')
                      ->with('http://example.com?ip=12.215.42.19&position=true')
                      ->will($this->returnValue($response));

        $this->geocoder->setApiUri('http://example.com');

        $this->geocoder->geocodeIp('12.215.42.19');
    }

    public function testGeocodeAddressZeroResultsResponse()
    {
        $response = \Geokit\Tests\TestHelper::createBuzzResponseFromString('HTTP/1.0 200 OK
Content-Type: text/xml; charset=iso-8859-1

<?xml version="1.0" encoding="ISO-8859-1" ?>
<HostipLookupResultSet version="1.0.1" xmlns:gml="http://www.opengis.net/gml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.hostip.info/api/hostip-1.0.1.xsd">
 <gml:description>This is the Hostip Lookup Service</gml:description>
 <gml:name>hostip</gml:name>
 <gml:boundedBy>
  <gml:Null>inapplicable</gml:Null>
 </gml:boundedBy>
 <gml:featureMember>

  <Hostip>
   <ip>foo</ip>
   <gml:name>(Private Address)</gml:name>
   <countryName>(Private Address)</countryName>
   <countryAbbrev>XX</countryAbbrev>
   <!-- Co-ordinates are unavailable -->
  </Hostip>

 </gml:featureMember>
</HostipLookupResultSet>
');

        $this->browser->expects($this->once())
                      ->method('get')
                      ->will($this->returnValue($response));

        $response = $this->geocoder->geocodeIp('foo');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(404, $response->getCode());
        $this->assertNull($response->getLocation());
    }

    public function testGeocodeIp()
    {
        $response = \Geokit\Tests\TestHelper::createBuzzResponseFromString('HTTP/1.0 200 OK
Content-Type: text/xml; charset=iso-8859-1

<?xml version="1.0" encoding="ISO-8859-1" ?>
<HostipLookupResultSet version="1.0.1" xmlns:gml="http://www.opengis.net/gml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://www.hostip.info/api/hostip-1.0.1.xsd">
 <gml:description>This is the Hostip Lookup Service</gml:description>
 <gml:name>hostip</gml:name>
 <gml:boundedBy>
  <gml:Null>inapplicable</gml:Null>
 </gml:boundedBy>
 <gml:featureMember>

  <Hostip>
   <ip>12.215.42.19</ip>
   <gml:name>Aurora, TX</gml:name>
   <countryName>UNITED STATES</countryName>
   <countryAbbrev>US</countryAbbrev>
   <!-- Co-ordinates are available as lng,lat -->
   <ipLocation>

    <gml:pointProperty>
     <gml:Point srsName="http://www.opengis.net/gml/srs/epsg.xml#4326">
      <gml:coordinates>-97.5159,33.0582</gml:coordinates>
     </gml:Point>
    </gml:pointProperty>
   </ipLocation>
  </Hostip>
 </gml:featureMember>

</HostipLookupResultSet>
');

        $this->browser->expects($this->once())
                      ->method('get')
                      ->will($this->returnValue($response));

        $response = $this->geocoder->geocodeIp('12.215.42.19');

        $this->assertInstanceOf('\Geokit\Geocoding\Response', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertSame($this->geocoder, $response->getGeocoder());

        $location = $response->getLocation();

        $this->assertInstanceOf('\Geokit\Geocoding\LocationInterface', $location);
        $this->assertEquals(new LatLng(33.0582, -97.5159), $location->getLatLng());
        $this->assertEquals(LocationInterface::ACCURACY_UNKNOWN, $location->getAccuracy());
        $this->assertEquals(null, $location->getBounds());
        $this->assertEquals(null, $location->getViewport());
        $this->assertEquals(null, $location->getFormattedAddress());
        $this->assertEquals(null, $location->getStreetNumber());
        $this->assertEquals(null, $location->getStreetName());
        $this->assertEquals(null, $location->getPostalCode());
        $this->assertEquals('Aurora, TX', $location->getLocality());
        $this->assertEquals(null, $location->getRegion());
        $this->assertEquals('UNITED STATES', $location->getCountryName());
        $this->assertEquals('US', $location->getCountryCode());
    }
}
