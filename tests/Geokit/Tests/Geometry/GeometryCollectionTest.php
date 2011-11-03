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

use Geokit\Geometry\GeometryCollection;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\GeometryCollection
 */
class GeometryCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testGeometryCollectionExtendsCollection()
    {
        $geometryCollection = new GeometryCollection();

        $this->assertInstanceOf('\Geokit\Geometry\Collection', $geometryCollection);
    }
}
