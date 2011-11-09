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

use Geokit\Geometry\LinearRing;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\LinearRing
 */
class LinearRingTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $points = array(new Point(1, 2), new Point(3, 4), new Point(1, 2));
        $linearRing = new LinearRing($points);
        $this->assertEquals($points, $linearRing->all());

        $this->setExpectedException('\InvalidArgumentException', 'LinearRing must have at least two points');

        $linearRing = new LinearRing(array(new Point(1, 2)));
    }

    public function testToString()
    {
        $linearRing = new LinearRing(array(new Point(1, 2), new Point(3, 4)));

        $this->assertEquals(
            sprintf('LINEARRING(%F %F,%F %F,%F %F)', 1, 2, 3, 4, 1, 2),
            $linearRing->__toString(),
            '__toString() returns WKT'
        );
    }

    public function testAdd()
    {
        $linearRing = new LinearRing(array(new Point(1, 2), new Point(3, 4)));

        $linearRing->add(new Point(5, 6));

        $this->assertEquals(array(new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(1, 2)), $linearRing->all());
    }

    public function testAddWithIndex()
    {
        $linearRing = new LinearRing(array(new Point(1, 2), new Point(3, 4)));

        $linearRing->add(new Point(1, 2), 2);

        $this->assertEquals(array(new Point(1, 2), new Point(3, 4), new Point(1, 2), new Point(1, 2)), $linearRing->all());
    }

    public function testGetArea()
    {
        $points = array(
            new Point(0, 0),
            new Point(0, 10),
            new Point(10, 10),
            new Point(10, 0)
        );

        $linearRing= new LinearRing($points);

        $this->assertEquals(100, $linearRing->getArea());
    }
}
