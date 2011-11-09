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
class Polygon extends GeometryCollection
{
    /**
     * {@inheritDoc}
     */
    protected $componentGeometryTypes = array('LinearRing');

    /**
     * Constructor.
     *
     * @param array $linearRings The LinearRings array
     */
    public function __construct(array $linearRings)
    {
        parent::__construct($linearRings);

        if ($this->count() < 1) {
            throw new \InvalidArgumentException(
                sprintf('%s must have at least an exterior ring', $this->getGeometryType())
            );
        }
    }
}
