<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit;

/**
 * The Earth's equatorial radius in meters, assuming the Earth is a perfect sphere.
 *
 * @see http://en.wikipedia.org/wiki/Earth_radius#Equatorial_radius
 */
define('EARTH_EQUATORIAL_RADIUS', 6378137.0);

/**
 * The Earth's polar radius.
 *
 * @see http://en.wikipedia.org/wiki/Earth_radius#Polar_radius
 */
define('EARTH_POLAR_RADIUS', 6356752.314245);

/**
 * The Earth's flattening.
 *
 * @see http://en.wikipedia.org/wiki/Earth_radius#Physics_of_Earth.27s_deformation
 */
define('EARTH_FLATTENING', 1/298.257223563);
