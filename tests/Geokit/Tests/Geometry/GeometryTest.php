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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Geometry
 */
class GeometryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetGeometryType()
    {
        $testGeometry1 = new Fixtures\TestGeometry1();
        $this->assertEquals('TestGeometry1', $testGeometry1->getGeometryType());

        $testGeometry2 = new Fixtures\TestGeometry2();
        $this->assertEquals('CustomTestGeometry2', $testGeometry2->getGeometryType());
    }

    public function testToString()
    {
        $testGeometry2 = new Fixtures\TestGeometry2();
        $this->assertSame('', $testGeometry2->__toString());
    }
}
