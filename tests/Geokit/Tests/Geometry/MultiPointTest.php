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

use Geokit\Geometry\MultiPoint;
use Geokit\Geometry\LineString;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\MultiPoint
 */
class MultiPointTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $points = array(new Point(1, 2), new Point(3, 4));
        $multiPoint = new MultiPoint($points);
        $this->assertEquals($points, $multiPoint->all());

        $multiPoint = new MultiPoint(array(new LineString($points)));
        $this->assertCount(0, $multiPoint->all());
    }

    public function testToString()
    {
        $points = array(new Point(1, 2), new Point(3, 4));
        $multiPoint = new MultiPoint($points);

        $this->assertEquals(
            sprintf('MULTIPOINT((%F %F),(%F %F))', 1, 2, 3, 4),
            $multiPoint->__toString(),
            '__toString() returns WKT'
        );
    }
}
