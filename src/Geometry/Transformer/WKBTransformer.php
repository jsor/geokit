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

        if (null === ($extracted = $this->extract($geometry))) {
            return null;
        }

        $wkb .= $extracted;

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
     * @param string $str A WKB string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str)
    {
        return $this->doReverseTransform($str);
    }

    /**
     * Do reverse-transformation of a WKB representation into a Geometry object.
     *
     * @param string $str A WKB string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function doReverseTransform(&$str)
    {
        $base = unpack('corder/Ltype', substr($str, 0, 5));
        $str = substr($str, 5);

        if (1 !== $base['order']) {
            throw new \InvalidArgumentException('Only NDR (little endian) is supported');
        }

        return $this->parse($base['type'], $str);
    }

    /**
     * Parse a WKB string into a Geometry object.
     *
     * @param string $type The Geometry type
     * @param string $str The WKT string
     * @return \Geokit\Geometry\GeometryInterface
     */
    protected function parse($type, &$str)
    {
        switch ($type) {
            case 1:
            case 'POINT':
                return $this->parsePoint($str);
            case 4:
            case 'MULTIPOINT':
                $num = unpack('L', substr($str, 0, 4));
                $str = substr($str, 4);

                $components = array();
                for ($i = 0; $i < $num[1]; $i++) {
                    $components[] = $this->doReverseTransform($str);
                }
                return new MultiPoint($components);
            case 2:
            case 'LINESTRING':
                $num = unpack('L', substr($str, 0, 4));
                $str = substr($str, 4);

                $components = array();
                for ($i = 0; $i < $num[1]; $i++) {
                    $components[] = $this->parse('POINT', $str);
                }
                return new LineString($components);
            case 5:
            case 'MULTILINESTRING':
                $num = unpack('L', substr($str, 0, 4));
                $str = substr($str, 4);

                $components = array();
                for ($i = 0; $i < $num[1]; $i++) {
                    $components[] = $this->doReverseTransform($str);
                }
                return new MultiLineString($components);
            case 3:
            case 'POLYGON':
                $num = unpack('L', substr($str, 0, 4));
                $str = substr($str, 4);

                $components = array();
                for ($i = 0; $i < $num[1]; $i++) {
                    $linestring = $this->parse('LINESTRING', $str);
                    $components[] = new LinearRing($linestring->all());
                }
                return new Polygon($components);
            case 6:
            case 'MULTIPOLYGON':
                $num = unpack('L', substr($str, 0, 4));
                $str = substr($str, 4);

                $components = array();
                for ($i = 0; $i < $num[1]; $i++) {
                    $components[] = $this->doReverseTransform($str);
                }
                return new MultiPolygon($components);
            case 7:
            case 'GEOMETRYCOLLECTION':
                $num = unpack('L', substr($str, 0, 4));
                $str = substr($str, 4);

                $components = array();
                for ($i = 0; $i < $num[1]; $i++) {
                    $components[] = $this->doReverseTransform($str);
                }
                return new GeometryCollection($components);
            default:
                return null;
        }
    }

    /**
     * Parses a point object.
     *
     * @param string $str
     * @return \Geokit\Geometry\Point
     */
    protected function parsePoint(&$str)
    {
        $coords = unpack('d*', substr($str, 0, 16));
        $str = substr($str, 16);

        return new Point($coords[1], $coords[2]);
    }
}
