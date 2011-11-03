<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests\Geometry\Fixtures;

use Geokit\Geometry\GeometryInterface;
use Geokit\Geometry\Geometry;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Transformer\WKTTransformer
 */
class TestGeometry1 extends Geometry
{
    /**
     * {@inheritDoc}
     */
    public function equals(GeometryInterface $geometry)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getBounds()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getCentroid()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return 'TestGeometry1';
    }
}
