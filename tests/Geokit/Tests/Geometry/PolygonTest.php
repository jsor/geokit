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

use Geokit\Geometry\Polygon;
use Geokit\Geometry\LinearRing;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Polygon
 */
class PolygonTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $points = array(new Point(1, 2), new Point(3, 4), new Point(1, 2));
        $linearRings = array(new LinearRing($points));
        $polygon = new Polygon($linearRings);
        $this->assertEquals($linearRings, $polygon->all());

        $this->setExpectedException('\InvalidArgumentException', 'Polygon must have at least an exterior ring');

        $linearRing = new Polygon(array(new Point(1, 2)));
    }

    public function testToString()
    {
        $points = array(new Point(1, 2), new Point(3, 4), new Point(1, 2));
        $linearRings = array(new LinearRing($points));
        $polygon = new Polygon($linearRings);

        $this->assertEquals(
            sprintf('POLYGON((%F %F,%F %F,%F %F))', 1, 2, 3, 4, 1, 2),
            $polygon->__toString(),
            '__toString() returns WKT'
        );
    }
}
