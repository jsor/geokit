<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Geometry\Transformer;

use Geokit\Geometry\GeometryInterface;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
interface TransformerInterface
{
    /**
     * Transforms a Geometry into another format (i.e. WKT, GeoJSON).
     *
     * @param \Geokit\Geometry\GeometryInterface
     * @return string
     */
    public function transform(GeometryInterface $geometry);

    /**
     * Reverse-transforms a representation (i.e. WKT, GeoJSON) into a Geometry object.
     *
     * @param string $str
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str);
}
