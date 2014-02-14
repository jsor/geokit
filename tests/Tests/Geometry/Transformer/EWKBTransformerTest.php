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

use Geokit\Geometry\Transformer\EWKBTransformer;
use Geokit\Geometry\Point;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Transformer\EWKBTransformer
 */
class EWKBTransformerTest extends AbstractWKBTransformerTest
{
    /**
     * @dataProvider ewkbDataProvider
     */
    public function testReverseTransform($a, $b, $message)
    {
        $transformer = new EWKBTransformer();

        $this->assertEquals($b, $transformer->reverseTransform(pack('H*', $a)), sprintf($message, 'reverseTransform()'));
    }

    public function testReverseTransformThrowsExceptionForXDR()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Only NDR (little endian) is supported');

        $transformer = new EWKBTransformer();
        $this->assertNull($transformer->reverseTransform(pack('cLdd', 0, 1, 1, 1)));
    }

    public function testReverseTransformWithZM()
    {
        $transformer = new EWKBTransformer();

        $str = '01010000C0000000000000F03F000000000000004000000000000008400000000000001040';
        $point = new Point(1, 2);

        $this->assertEquals($point, $transformer->reverseTransform(pack('H*', $str)));
    }
}
