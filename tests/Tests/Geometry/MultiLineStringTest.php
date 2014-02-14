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

use Geokit\Geometry\MultiLineString;
use Geokit\Geometry\LineString;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\MultiLineString
 */
class MultiLineStringTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $points = array(new Point(1, 2), new Point(3, 4));
        $lineStrings = array(new LineString($points), new LineString($points));
        $multiLineString = new MultiLineString($lineStrings);
        $this->assertEquals($lineStrings, $multiLineString->all());

        $multiLineString = new MultiLineString(array(new Point(1, 2)));
        $this->assertCount(0, $multiLineString->all());
    }

    public function testToString()
    {
        $points = array(new Point(1, 2), new Point(3, 4));
        $lineStrings = array(new LineString($points), new LineString($points));
        $multiLineString = new MultiLineString($lineStrings);

        $this->assertEquals(
            sprintf('MULTILINESTRING((%F %F,%F %F),(%F %F,%F %F))', 1, 2, 3, 4, 1, 2, 3, 4),
            $multiLineString->__toString(),
            '__toString() returns WKT'
        );
    }
}
