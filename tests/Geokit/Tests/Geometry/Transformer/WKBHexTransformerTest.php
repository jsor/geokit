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

use Geokit\Geometry\Transformer\WKBHexTransformer;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Transformer\WKBHexTransformer
 */
class WKBHexTransformerTest extends AbstractWKBTransformerTest
{
    /**
     * @dataProvider wkbDataProvider
     */
    public function testTransform($a, $b, $message)
    {
        $transformer = new WKBHexTransformer();

        $this->assertEquals($a, $transformer->transform($b), sprintf($message, 'transform()'));
    }

    /**
     * @dataProvider wkbDataProvider
     */
    public function testReverseTransform($a, $b, $message)
    {
        $transformer = new WKBHexTransformer();

        $this->assertEquals($b, $transformer->reverseTransform($a), sprintf($message, 'reverseTransform()'));
    }
}
