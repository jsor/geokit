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

use Geokit\Geometry\LineString;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\LineString
 */
class LineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $points = array(new Point(1, 2), new Point(3, 4));
        $lineString = new LineString($points);
        $this->assertEquals($points, $lineString->all());

        $this->setExpectedException('\InvalidArgumentException', 'LineString must have at least two points');

        $lineString = new LineString(array(new Point(1, 2)));
    }

    public function testToString()
    {
        $lineString = new LineString(array(new Point(1, 2), new Point(3, 4)));

        $this->assertEquals(
            sprintf('LINESTRING(%F %F,%F %F)', 1, 2, 3, 4),
            $lineString->__toString(),
            '__toString() returns WKT'
        );
    }
}
