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

use Geokit\Geocoding\Response;
use Geokit\Geocoding\GeocoderInterface;
use Geokit\Geocoding\LocationInterface;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geocoding\Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorArguments()
    {
        $geocoder = $this->getMock('\Geokit\Geocoding\GeocoderInterface');
        $location = $this->getMock('\Geokit\Geocoding\LocationInterface');
        $location2 = $this->getMock('\Geokit\Geocoding\LocationInterface');

        $response = new Response(
            200,
            $geocoder,
            $location,
            array($location2)
        );

        $this->assertSame(200, $response->getCode());
        $this->assertSame($geocoder, $response->getGeocoder());
        $this->assertSame($location, $response->getLocation());
        $this->assertCount(1, $response->getAdditionalLocations());
        $this->assertContains($location2, $response->getAdditionalLocations());
    }

    public function testIsValid()
    {
        $geocoder = $this->getMock('\Geokit\Geocoding\GeocoderInterface');
        $location = $this->getMock('\Geokit\Geocoding\LocationInterface');
        $location2 = $this->getMock('\Geokit\Geocoding\LocationInterface');

        $response = new Response(
            100,
            $geocoder,
            $location,
            array($location2)
        );

        $this->assertFalse($response->isValid());

        $response = new Response(
            200,
            $geocoder,
            $location,
            array($location2)
        );

        $this->assertTrue($response->isValid());

        $response = new Response(
            301,
            $geocoder,
            $location,
            array($location2)
        );

        $this->assertFalse($response->isValid());

    }
}
