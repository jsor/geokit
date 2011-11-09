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
 * Ported from https://github.com/openlayers/openlayers/blob/master/lib/OpenLayers/Format/WKT.js
 *
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class WKTTransformer implements TransformerInterface
{
    private $regExes = array(
        'typeStr'          => '/^\s*(\w+)\s*\(\s*(.*)\s*\)\s*$/s',
        'spaces'           => '/\s+/',
        'parenComma'       => '/\)\s*,\s*\(/',
        'doubleParenComma' => '/\)\s*\)\s*,\s*\(\s*\(/', // can't use {2} here
        'trimParens'       => '/^\s*\(?(.*?)\)?\s*$/'
    );

    /**
     * Whether to transform MultiPoints PostGIS compliant
     *
     * The spec requires parens around the point (MultiPoint((1 1),(2 2)))
     * while PostGIS does not (MultiPoint(1 1,2 2)).
     *
     * @var boolean
     */
    protected $postGisCompliant;

    /**
     * Constructor
     *
     * @param boolean $postGisCompliant
     */
    public function __construct($postGisCompliant = false)
    {
        $this->postGisCompliant = $postGisCompliant;
    }

    /**
     * Transforms a Geometry into a WKT string.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string The WKT string representation of the geometry
     */
    public function transform(GeometryInterface $geometry)
    {
        if (null === ($data = $this->extract($geometry))) {
            return null;
        }

        return sprintf('%s(%s)', strtoupper($geometry->getGeometryType()), $data);
    }

    /**
     * Extract a Geometry to a WKT string.
     *
     * @param \Geokit\Geometry\GeometryInterface $geometry
     * @return string
     */
    protected function extract(GeometryInterface $geometry)
    {
        switch (strtoupper($geometry->getGeometryType())) {
            case 'POINT':
                return sprintf('%F %F', $geometry->getX(), $geometry->getY());
            case 'LINESTRING':
            case 'LINEARRING':
                $array = array();
                foreach ($geometry->all() as $component) {
                    $array[] = $this->extract($component);
                }
                return implode(',', $array);
            case 'MULTIPOINT':
                $pattern = $this->postGisCompliant ? '%s' : '(%s)';
                $array = array();
                foreach ($geometry->all() as $component) {
                    $array[] = sprintf($pattern, $this->extract($component));
                }
                return implode(',', $array);
            case 'MULTILINESTRING':
            case 'POLYGON':
            case 'MULTIPOLYGON':
                $array = array();
                foreach ($geometry->all() as $component) {
                    $array[] = sprintf('(%s)', $this->extract($component));
                }
                return implode(',', $array);
            case 'GEOMETRYCOLLECTION':
                $array = array();
                foreach ($geometry->all() as $component) {
                    $array[] = sprintf('%s(%s)', strtoupper($component->getGeometryType()), $this->extract($component));
                }
                return implode(',', $array);
            default:
                return null;
        }
    }

    /**
     * Reverse-transforms a WKT representation into a Geometry object.
     *
     * @param string $str A WKT string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str)
    {
        $matches = array();
        if (!preg_match($this->regExes['typeStr'], $str, $matches)) {
            return null;
        }

        return $this->parse($matches[1], $matches[2]);
    }

    /**
     * Parse a WKT string into a Geometry object.
     *
     * @param string $type The Geometry type
     * @param string $str The WKT string
     * @return \Geokit\Geometry\GeometryInterface
     */
    protected function parse($type, $str)
    {
        switch (strtoupper($type)) {
            case 'POINT':
                $coords = preg_split($this->regExes['spaces'], trim($str));
                return new Point((float) $coords[0],(float) $coords[1]);
            case 'MULTIPOINT':
                $components = array();
                foreach (explode(',', trim($str)) as $point) {
                    $point = preg_replace($this->regExes['trimParens'], '$1', $point);
                    $components[] = $this->parse('POINT', $point);
                }
                return new MultiPoint($components);
            case 'LINESTRING':
                $components = array();
                foreach (explode(',', trim($str)) as $point) {
                    $components[] = $this->parse('POINT', $point);
                }
                return new LineString($components);
            case 'MULTILINESTRING':
                $lines      = preg_split($this->regExes['parenComma'], trim($str));
                $components = array();
                foreach ($lines as $line) {
                    $line = preg_replace($this->regExes['trimParens'], '$1', $line);
                    $components[] = $this->parse('LINESTRING', $line);
                }
                return new MultiLineString($components);
            case 'POLYGON':
                $rings      = preg_split($this->regExes['parenComma'], trim($str));
                $components = array();
                foreach ($rings as $ring) {
                    $ring = preg_replace($this->regExes['trimParens'], '$1', $ring);
                    $linestring = $this->parse('LINESTRING', $ring);
                    $components[] = new LinearRing($linestring->all());
                }
                return new Polygon($components);
            case 'MULTIPOLYGON':
                $polygons   = preg_split($this->regExes['doubleParenComma'], trim($str));
                $components = array();
                foreach ($polygons as $polygon) {
                    $polygon = preg_replace($this->regExes['trimParens'], '$1', $polygon);
                    $components[] = $this->parse('POLYGON', $polygon);
                }
                return new MultiPolygon($components);
            case 'GEOMETRYCOLLECTION':
                $str        = preg_replace('/,\s*([A-Za-z])/', '|$1', $str);
                $wktArray   = explode('|', trim($str));
                $components = array();
                foreach ($wktArray as $wkt) {
                    $components[] = $this->reverseTransform($wkt);
                }
                return new GeometryCollection($components);
            default:
                return null;
        }
    }
}
