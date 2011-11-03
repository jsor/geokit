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
class GeometryCollection extends Collection
{
    /**
     * Constructor.
     *
     * @param array $geometries The Geometries array
     */
    public function __construct(array $geometries = array())
    {
        parent::__construct($geometries);
    }
}
