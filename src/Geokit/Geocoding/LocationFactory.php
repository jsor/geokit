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
class LocationFactory implements LocationFactoryInterface
{
    /**
     * @var string
     */
    private $locationClass = '\Geokit\Geocoding\Location';

    /**
     * @param string $locationClass
     */
    public function __construct($locationClass = null)
    {
        if (null !== $locationClass) {
            $this->locationClass = $locationClass;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function createLocation(LatLng $latLng,
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
        return new $this->locationClass(
            $latLng,
            $accuracy,
            $bounds,
            $viewport,
            $formattedAddress,
            $streetNumber,
            $streetName,
            $postalCode,
            $locality,
            $region,
            $countryName,
            $countryCode
        );
    }
}
