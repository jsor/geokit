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
interface LocationInterface
{
    /**
     * @see http://code.google.com/intl/de-DE/apis/maps/documentation/geocoding/#Results
     */
    const ACCURACY_PRECISE      = 4;
    const ACCURACY_INTERPOLATED = 3;
    const ACCURACY_CENTER       = 2;
    const ACCURACY_APPROXIMATE  = 1;
    const ACCURACY_UNKNOWN      = 0;

    /**
     * @return \Geokit\LatLng
     */
    function getLatLng();

    /**
     * @return integer
     */
    function getAccuracy();
}
