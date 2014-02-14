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
use Geokit\Geocoding\LocationInterface;
use Geokit\Geocoding\Location;
use Geokit\LatLng;
use Geokit\Bounds;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 
 * @covers Geokit\Geocoding\Location
 */
class LocationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorArguments()
    {
        $latLng   = new LatLng(1.1234, 2.5678);
        $bounds   = new Bounds(new LatLng(1.1234, 2.5678), new LatLng(1.1234, 2.5678));
        $viewport = new Bounds(new LatLng(1.1234, 2.5678), new LatLng(1.1234, 2.5678));

        $location = new Location(
            $latLng,
            LocationInterface::ACCURACY_PRECISE,
            $bounds,
            $viewport,
            'formattedAddress',
            'streetNumber',
            'streetName',
            'postalCode',
            'locality',
            'region',
            'countryName',
            'countryCode'
        );

        $this->assertInstanceOf('\Geokit\Geocoding\LocationInterface', $location);
        $this->assertSame($latLng, $location->getLatLng());
        $this->assertSame(LocationInterface::ACCURACY_PRECISE, $location->getAccuracy());
        $this->assertSame($bounds, $location->getBounds());
        $this->assertSame($viewport, $location->getViewport());
        $this->assertEquals('formattedAddress', $location->getFormattedAddress());
        $this->assertEquals('streetNumber', $location->getStreetNumber());
        $this->assertEquals('streetName', $location->getStreetName());
        $this->assertEquals('postalCode', $location->getPostalCode());
        $this->assertEquals('locality', $location->getLocality());
        $this->assertEquals('region', $location->getRegion());
        $this->assertEquals('countryName', $location->getCountryName());
        $this->assertEquals('countryCode', $location->getCountryCode());
    }
}
