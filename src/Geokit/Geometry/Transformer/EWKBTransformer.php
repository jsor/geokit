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
 * This class is actually a EWKB reader only. Since Geokit doesn't support
 * SRIDs and 2D geometries only, transform() actually returns WKB (which is
 * also valid EWKB).
 *
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class EWKBTransformer extends WKBTransformer
{
    const Z_MASK    = 0x80000000;
    const M_MASK    = 0x40000000;
    const SRID_MASK = 0x20000000;

    const UNDEFINED_SRID = -1;

    private $srid  = null;
    private $withZ = false;
    private $withM = false;

    /**
     * Reverse-transforms a EWKB representation into a Geometry object.
     *
     * @param string $str A EWKB string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function reverseTransform($str)
    {
        $this->srid  = null;
        $this->withZ = false;
        $this->withM = false;

        return parent::reverseTransform($str);
    }

    /**
     * Do reverse-transformation of a EWKB representation into a Geometry object.
     *
     * @param string $str A EWKB string
     * @return \Geokit\Geometry\GeometryInterface
     */
    public function doReverseTransform(&$str)
    {
        $base = unpack('corder/Ltype', $str);
        $str = substr($str, 5);

        if (1 !== $base['order']) {
            throw new \InvalidArgumentException('Only NDR (little endian) is supported');
        }

        $type = $base['type'];

        if ($type & self::Z_MASK) {
            $this->withZ = true;
            $type = ($type & ~self::Z_MASK);
        }

        if ($type & self::M_MASK) {
            $this->withM = true;
            $type = ($type & ~self::M_MASK);
        }

        if ($type & self::SRID_MASK) {
            $srid = unpack('L', substr($str, 0, 4));
            $str = substr($str, 4);

            $this->srid = $srid[1];
            $type = $type & ~self::SRID_MASK;
        } elseif (!$this->srid) {
            $this->srid = self::UNDEFINED_SRID;
        }

        return $this->parse($type, $str);
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

        if ($this->withZ) {
            $z = unpack('d', substr($str, 0, 8));
            $str = substr($str, 8);
        }

        if ($this->withM) {
            $m = unpack('d', substr($str, 0, 8));
            $str = substr($str, 8);
        }

        return new Point($coords[1], $coords[2]);
    }
}
