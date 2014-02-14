<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Geocoding;

use Buzz\Browser;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
abstract class AbstractGeocoder implements GeocoderInterface
{
    /**
     * @var \Geokit\Geocoding\LocationFactoryInterface
     */
    private $locationFactory;

    /**
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * @param \Geokit\Geocoding\LocationFactoryInterface $locationFactory
     * @param \Buzz\Browser $browser
     */
    public function __construct(LocationFactoryInterface $locationFactory = null, Browser $browser = null)
    {
        $this->locationFactory = $locationFactory ?: new LocationFactory();
        $this->browser         = $browser ?: new Browser();
    }

    /**
     * Set the location factory.
     *
     * @param \Geokit\Geocoding\LocationFactoryInterface $locationFactory
     * @return AbstractGeocoder
     */
    public function setLocationFactory(LocationFactoryInterface $locationFactory)
    {
        $this->locationFactory = $locationFactory;
        return $this;
    }

    /**
     * Get the location factory.
     *
     * @return \Geokit\Geocoding\LocationFactoryInterface
     */
    public function getLocationFactory()
    {
        return $this->locationFactory;
    }

    /**
     * Set Browser.
     *
     * @param \Buzz\Browser $browser
     * @return AbstractGeocoder
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
        return $this;
    }

    /**
     * Get Browser.
     *
     * @return \Buzz\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * {@inheritDoc}
     */
    public function geocodeAddress($address)
    {
        return new Response(501, $this); // Not implemented
    }

    /**
     * {@inheritDoc}
     */
    public function reverseGeocodeLatLng($latLng)
    {
        return new Response(501, $this); // Not implemented
    }

    /**
     * {@inheritDoc}
     */
    public function geocodeIp($ip)
    {
        return new Response(501, $this); // Not implemented
    }
}
