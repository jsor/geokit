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
 * MySQL has a non-standard method of storing and retrieving spatial objects.
 * It almost matches the OGS, using the Well Known Binary format, but breaks
 * compatibility by pre-pending a 4-byte (the SRID) value to the WKB blob.
 * Since Geokit ignores SRIDs, we simple cut it off.
 *
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class MySQLWKBTransformer extends WKBTransformer
{
    /**
     * Transforms a Geometry into a MySQL string.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string The MySQL representation of the geometry
     */
    public function transform(GeometryInterface $geometry)
    {
        return pack('xxxx').parent::transform($geometry);
    }

    /**
     * Reverse-transforms a MySQL representation into a Geometry object.
     *
     * @param string $str A MySQL string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str)
    {
        return parent::reverseTransform(substr($str, 4));
    }
}
