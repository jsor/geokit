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

use Geokit\LatLng;
use Geokit\Bounds;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class Location implements LocationInterface
{
    /**
     * @var \Geokit\LatLng
     */
    private $latLng;

    /**
     * @var integer
     */
    private $accuracy;

    /**
     * @var \Geokit\Bounds
     */
    private $bounds;

    /**
     * @var \Geokit\Bounds
     */
    private $viewport;

    /**
     * @var string
     */
    private $formattedAddress;

    /**
     * @var string
     */
    private $streetNumber;

    /**
     * @var string
     */
    private $streetName;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $locality;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $countryName;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @param \Geokit\LatLng $latLng
     * @param integer $accuracy
     * @param \Geokit\Bounds $bounds
     * @param \Geokit\Bounds $viewport
     * @param string $formattedAddress
     * @param string $streetNumber
     * @param string $streetName
     * @param string $postalCode
     * @param string $locality
     * @param string $region
     * @param string $countryName
     * @param string $countryCode
     */
    public function __construct(LatLng $latLng,
                                $accuracy = LocationInterface::ACCURACY_UNKNOWN,
                                Bounds $bounds = null,
                                Bounds $viewport = null,
                                $formattedAddress = null,
                                $streetNumber = null,
                                $streetName = null,
                                $postalCode = null,
                                $locality = null,
                                $region = null,
                                $countryName = null,
                                $countryCode = null)
    {
        $this->latLng           = $latLng;
        $this->accuracy         = $accuracy;
        $this->bounds           = $bounds;
        $this->viewport         = $viewport;
        $this->formattedAddress = $formattedAddress;
        $this->streetNumber     = $streetNumber;
        $this->streetName       = $streetName;
        $this->postalCode       = $postalCode;
        $this->locality         = $locality;
        $this->region           = $region;
        $this->countryName      = $countryName;
        $this->countryCode      = $countryCode;
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getLatLng()
    {
        return $this->latLng;
    }

    /**
     * @return integer
     */
    public function getAccuracy()
    {
        return $this->accuracy;
    }

    /**
     * @return \Geokit\Bounds
     */
    public function getBounds()
    {
        return $this->bounds;
    }

    /**
     * @return \Geokit\Bounds
     */
    public function getViewport()
    {
        return $this->viewport;
    }

    /**
     * @return string
     */
    public function getFormattedAddress()
    {
        return $this->formattedAddress;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @return string
     */
    public function getStreetname()
    {
        return $this->streetName;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}
