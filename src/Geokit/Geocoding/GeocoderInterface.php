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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
interface GeocoderInterface
{
    /**
     * @param string $address
     * @return \Geokit\Geocoding\Response
     */
    function geocodeAddress($address);

    /**
     * @param \Geokit\LatLng $latLng
     * @return \Geokit\Geocoding\Response
     */
    function reverseGeocodeLatLng(LatLng $latLng);

    /**
     * @param string $ip
     * @return \Geokit\Geocoding\Response
     */
    function geocodeIp($ip);
}
