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

use Geokit\Geometry\Point;
use Geokit\LatLng;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Point
 */
class PointTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $point = new Point(1, 2);

        $this->assertSame((float) 1, $point->getX());
        $this->assertSame((float) 2, $point->getY());
    }

    public function testEquals()
    {
        $point = new Point(1, 2);

        $this->assertTrue($point->equals(new Point(1, 2)));
        $this->assertFalse($point->equals(new Point(1, 3)));
        $this->assertFalse($point->equals(new Point(2, 2)));
        $this->assertFalse($point->equals(new Point(3, 4)));
        $this->assertFalse($point->equals(new Fixtures\TestGeometry1()));
    }

    public function testGetBounds()
    {
        $point = new Point(1, 2);

        $this->assertInstanceOf('\Geokit\Bounds', $point->getBounds());
        $this->assertEquals(new LatLng(2, 1), $point->getBounds()->getSouthWest());
        $this->assertEquals(new LatLng(2, 1), $point->getBounds()->getNorthEast());
    }

    public function testGetCentroid()
    {
        $point = new Point(1, 2);

        $this->assertEquals($point, $point->getCentroid());
        $this->assertNotSame($point, $point->getCentroid(), 'getCentroid() returns clone');
    }

    public function testToArray()
    {
        $point = new Point(1, 2);
        $expected = array((float) 1, (float) 2);

        $this->assertSame($expected, $point->toArray());
    }

    public function testToString()
    {
        $point = new Point(1, 2);

        $this->assertEquals(
            sprintf('POINT(%F %F)', 1, 2),
            $point->__toString(),
            '__toString() returns WKT'
        );
    }
}
