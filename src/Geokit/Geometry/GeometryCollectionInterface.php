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
interface GeometryCollectionInterface extends GeometryInterface
{
    /**
     * Add a component.
     *
     * @param GeometryInterface $component
     * @param integer $index Optional index to insert the component into the array
     * @return boolean Whether the component was successfully added
     */
    function add(GeometryInterface $component, $index = null);

    /**
     * Returns all components.
     *
     * @return array
     */
    function all();

    /**
     * Calculate the area of this geometry.
     *
     * @return float The area of the collection by summing its parts
     */
    function getArea();
}
