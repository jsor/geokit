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

    /**
     * Calculated by subtracting the areas of the internal holes from the
     * area of the outer hole.
     *
     * @return float The area of the geometry
     */
    public function getArea()
    {
        $area = 0;
        if ($this->count() > 0) {
            $components = $this->all();
            $area += abs($components[0]->getArea());
            for ($i = 1, $len = count($components); $i < $len; $i++) {
                $area -= abs($components[$i]->getArea());
            }
        }

        return $area;
    }
}
