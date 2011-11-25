<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests\Geometry\Transformer;

use Geokit\Geometry\Transformer\WKBTransformer;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Transformer\WKBTransformer
 */
class WKBTransformerTest extends AbstractWKBTransformerTest
{
    /**
     * @dataProvider wkbDataProvider
     */
    public function testTransform($a, $b, $message)
    {
        $transformer = new WKBTransformer();

        $this->assertEquals(pack('H*', $a), $transformer->transform($b), sprintf($message, 'transform()'));
    }

    /**
     * @dataProvider wkbDataProvider
     */
    public function testReverseTransform($a, $b, $message)
    {
        $transformer = new WKBTransformer();

        $this->assertEquals($b, $transformer->reverseTransform(pack('H*', $a)), sprintf($message, 'reverseTransform()'));
    }

    public function testUnknownGeometry()
    {
        $transformer = new WKBTransformer();
        $this->assertNull($transformer->transform(new \Geokit\Tests\Geometry\Fixtures\TestGeometry1()), 'transform() returns NULL for unknown geometry');

        $this->assertNull($transformer->reverseTransform(pack('cL', 1, 8)), 'reverseTransform() returns NULL for undefined geometry');
    }

    public function testReverseTransformThrowsExceptionForXDR()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Only NDR (little endian) is supported');

        $transformer = new WKBTransformer();
        $this->assertNull($transformer->reverseTransform(pack('cLdd', 0, 1, 1, 1)));
    }
}
