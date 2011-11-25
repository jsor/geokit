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
use Geokit\Geometry\GeometryCollection;
use Geokit\Geometry\LineString;
use Geokit\Geometry\LinearRing;
use Geokit\Geometry\MultiLineString;
use Geokit\Geometry\MultiPoint;
use Geokit\Geometry\MultiPolygon;
use Geokit\Geometry\Point;
use Geokit\Geometry\Polygon;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class EWKBHexTransformer extends EWKBTransformer
{
    /**
     * Transforms a Geometry into a EWKB Hex string.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string The EWKB representation of the geometry
     */
    public function transform(GeometryInterface $geometry)
    {
        $ewkb = parent::transform($geometry);
        $unpacked = unpack('H*', $ewkb);
        return $unpacked[1];
    }

    /**
     * Reverse-transforms a EWKB Hex representation into a Geometry object.
     *
     * @param string $str A EWKB string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str)
    {
        return parent::reverseTransform(pack('H*', $str));
    }
}
