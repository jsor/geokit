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
interface GeocoderInterface
{
    /**
     * @param string $address
     * @return \Geokit\Geocoding\Response
     */
    function geocodeAddress($address);

    /**
     * @param mixed $latLng A latitude/longitude pair which can be normalized by \Geokit\Util::normalizeLatLng()
     * @return \Geokit\Geocoding\Response
     */
    function reverseGeocodeLatLng($latLng);

    /**
     * @param string $ip
     * @return \Geokit\Geocoding\Response
     */
    function geocodeIp($ip);
}
