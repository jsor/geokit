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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class Response
{
    /**
     * HTTP-like Status Code.
     *
     * @var integer
     */
    private $code;

    /**
     * @var \Geokit\Geocoding\GeocoderInterface
     */
    private $geocoder;

    /**
     * @var \Geokit\Geocoding\LocationInterface
     */
    private $location;

    /**
     * Additional locations
     *
     * @var array
     */
    protected $additionalLocations;

    /**
     * Sets the result code, geocoder and location.
     *
     * @param  integer $code
     * @param  \Geokit\Geocoding\GeocoderInterface $geocoder
     * @param  \Geokit\Geocoding\LocationInterface $location
     * @param  array $additionalResults
     * @return void
     */
    public function __construct($code, GeocoderInterface $geocoder, LocationInterface $location = null, array $additionalLocations = array())
    {
        $this->code                = (integer) $code;
        $this->geocoder            = $geocoder;
        $this->location            = $location;
        $this->additionalLocations = $additionalLocations;
    }

    /**
     * Check whether the result is valid.
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->code >= 200 && $this->code < 300;
    }

    /**
     * Get the result code.
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the geocoder.
     *
     * @return \Geokit\Geocoding\GeocoderInterface
     */
    public function getGeocoder()
    {
        return $this->geocoder;
    }

    /**
     * Get the result.
     *
     * @return \Geokit\Geocoding\LocationInterface
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get additional locations.
     *
     * @return array
     */
    public function getAdditionalLocations()
    {
        return $this->additionalLocations;
    }
}
