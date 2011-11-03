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
interface LocationFactoryInterface
{
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
     * @return \Geokit\LocationInterface
     */
    function createLocation(LatLng $latLng,
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
                            $countryCode = null);
}
