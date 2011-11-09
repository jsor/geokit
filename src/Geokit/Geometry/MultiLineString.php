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
class MultiLineString extends GeometryCollection
{
    /**
     * {@inheritDoc}
     */
    protected $componentGeometryTypes = array('LineString');

    /**
     * Constructor.
     *
     * @param array $lineStrings The LineStrings array
     */
    public function __construct(array $lineStrings)
    {
        parent::__construct($lineStrings);
    }
}
