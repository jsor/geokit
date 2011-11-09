<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Geometry;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
interface GeometryInterface
{
    /**
     * @return string The Geometry type
     */
    function getGeometryType();

    /**
     * Determine whether another geometry is equivalent to this one.
     *
     * @param \Geokit\Geometry\GeometryInterface
     * @return boolean
     */
    function equals(GeometryInterface $geometry);

    /**
     * @return \Geokit\Geometry\Point
     */
    function getCentroid();

    /**
     * @return array
     */
    function toArray();

    /**
     * @return string
     */
    function __toString();
}
