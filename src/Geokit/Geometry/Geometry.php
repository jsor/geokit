<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Geometry;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
abstract class Geometry implements GeometryInterface
{
    /**
     * @var string
     */
    protected $geometryType;

    /**
     * {@inheritDoc}
     */
    public function getGeometryType()
    {
        if (null !== $this->geometryType) {
            return $this->geometryType;
        }

        $name = get_class($this);
        $pos  = strrpos($name, '\\');

        return false === $pos ? $name :  substr($name, $pos + 1);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        $transformer = new Transformer\WKTTransformer(true); // Transform with PostGIS compliance
        return (string) $transformer->transform($this);
    }
}
