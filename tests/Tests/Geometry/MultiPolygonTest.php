<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests\Geometry;

use Geokit\Geometry\MultiPolygon;
use Geokit\Geometry\Polygon;
use Geokit\Geometry\LinearRing;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\MultiPolygon
 */
class MultiPolygonTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $points = array(new Point(1, 2), new Point(3, 4), new Point(1, 2));
        $linearRings = array(new LinearRing($points));
        $polygons = array(new Polygon($linearRings), new Polygon($linearRings));
        $multiPolygon = new MultiPolygon($polygons);
        $this->assertEquals($polygons, $multiPolygon->all());

        $multiPolygon = new MultiPolygon(array(new Point(1, 2)));
        $this->assertCount(0, $multiPolygon->all());
    }

    public function testToString()
    {
        $points = array(new Point(1, 2), new Point(3, 4), new Point(1, 2));
        $linearRings = array(new LinearRing($points));
        $polygons = array(new Polygon($linearRings), new Polygon($linearRings));
        $multiPolygon = new MultiPolygon($polygons);

        $this->assertEquals(
            sprintf('MULTIPOLYGON(((%F %F,%F %F,%F %F)),((%F %F,%F %F,%F %F)))', 1, 2, 3, 4, 1, 2, 1, 2, 3, 4, 1, 2),
            $multiPolygon->__toString(),
            '__toString() returns WKT'
        );
    }
}
