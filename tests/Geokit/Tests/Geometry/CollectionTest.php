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

use Geokit\Geometry\Collection;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $components = array(new Point(1, 2));
        $collection = $this->getMockForAbstractClass('\Geokit\Geometry\Collection', array($components));
        $this->assertCount(1, $collection);
        $this->assertEquals($components, $collection->all());
    }

    public function testAdd()
    {
        $components = array(new Point(1, 2), new Point(3, 4), new Point(5, 6));
        $collection = $this->getMockForAbstractClass('\Geokit\Geometry\Collection');
        $this->assertCount(0, $collection);
        $collection->add($components[0]);
        $this->assertCount(1, $collection);

        $collection->add($components[2]);
        $collection->add($components[1], 1);

        $this->assertCount(3, $collection);
        $this->assertEquals($components, $collection->all());

        $rc = new \ReflectionObject($collection);
        $reflField = $rc->getProperty('componentGeometryTypes');
        $reflField->setAccessible(true);
        $reflField->setValue($collection, array('LineString'));
        $this->assertFalse($collection->add(new Point(1, 2)));
    }

    public function testEquals()
    {
        $collection1 = $this->getMockForAbstractClass('\Geokit\Geometry\Collection');
        $collection2 = $this->getMockForAbstractClass('\Geokit\Geometry\Collection');
        $this->assertFalse($collection1->equals(new Point(1, 2)));
        $this->assertTrue($collection1->equals($collection2));

        $collection1->add(new Point(1, 2));
        $this->assertFalse($collection1->equals($collection2));
        $collection2->add(new Point(3, 4));
        $this->assertFalse($collection1->equals($collection2));
    }

    public function testGetCentroid()
    {
        $this->markTestSkipped('getCentroid() not implemented yet');

        $collection = $this->getMockForAbstractClass('\Geokit\Geometry\Collection');
        $collection->add(new Point(1, 1));
        $collection->add(new Point(10, 10));

        $this->assertInstanceOf('\Geokit\Geometry\Point', $collection->getCentroid());
        $this->assertEquals(new Point(5.5, 5.5), $collection->getCentroid());
    }

    public function testToArray()
    {
        $collection = $this->getMockForAbstractClass('\Geokit\Geometry\Collection');
        $collection->add(new Point(1, 2));
        $collection->add(new Point(3, 4));

        $expected = array(
            array(1, 2),
            array(3, 4)
        );

        $this->assertEquals($expected, $collection->toArray());
    }
}
