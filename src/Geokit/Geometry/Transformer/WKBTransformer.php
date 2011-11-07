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
class WKBTransformer implements TransformerInterface
{
    /**
     * Transforms a Geometry into a WKB string.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string The WKB representation of the geometry
     */
    public function transform(GeometryInterface $geometry)
    {
        if (null === ($wkb = $this->doTransform($geometry))) {
            return null;
        }

        return $wkb;
    }

    /**
     * Do transformation of a Geometry.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string The WKB representation of the geometry
     */
    protected function doTransform(GeometryInterface $geometry)
    {
        // We always write into NDR (little endian)
        $wkb = pack('c', 1);

        switch (strtoupper($geometry->getGeometryType())) {
            case 'POINT':
                $wkb .= pack('L', 1);
                break;
            case 'LINESTRING':
            case 'LINEARRING':
                $wkb .= pack('L', 2);
                break;
            case 'POLYGON':
                $wkb .= pack('L', 3);
                break;
            case 'MULTIPOINT':
                $wkb .= pack('L', 4);
                break;
            case 'MULTILINESTRING':
                $wkb .= pack('L', 5);
                break;
            case 'MULTIPOLYGON':
                $wkb .= pack('L', 6);
                break;
            case 'GEOMETRYCOLLECTION':
                $wkb .= pack('L', 7);
                break;
        }

        $wkb .= $this->extract($geometry);

        return $wkb;
    }

    /**
     * Extract a Geometry to a WKB string.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string
     */
    protected function extract(GeometryInterface $geometry)
    {
        switch (strtoupper($geometry->getGeometryType())) {
            case 'POINT':
                return pack('dd', $geometry->getX(), $geometry->getY());
            case 'LINESTRING':
            case 'LINEARRING':
            case 'POLYGON':
                $wkb = pack('L', $geometry->count());
                foreach ($geometry->all() as $component) {
                    $wkb .= $this->extract($component);
                }
                return $wkb;
            case 'MULTIPOINT':
            case 'MULTILINESTRING':
            case 'MULTIPOLYGON':
            case 'GEOMETRYCOLLECTION':
                $wkb = pack('L', $geometry->count());
                foreach ($geometry->all() as $component) {
                    $wkb .= $this->doTransform($component);
                }
                return $wkb;
        }
    }

    /**
     * Reverse-transforms a WKB representation into a Geometry object.
     *
     * @param string $str A WKT string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str)
    {
    }
}
