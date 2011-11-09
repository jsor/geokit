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
class LinearRing extends LineString
{
    /**
     * Constructor.
     *
     * @param array $points The Point array
     */
    public function __construct(array $points)
    {
        parent::__construct($points);

        if ($this->count() < 3) {
            throw new \InvalidArgumentException(
                sprintf('%s must have at least two points', $this->getGeometryType())
            );
        }
    }

    /**
     * Adds a point to geometry components.
     *
     * If the point is to be added to the end of the components array and
     * it is the same as the last point already in that array, the duplicate
     * point is not added. This has the effect of closing the ring if it is not
     * already closed, and doing the right thing if it is already closed.
     * This behavior can be overridden by calling the method with a non-null
     * index as the second argument.
     *
     * @param GeometryInterface $component
     * @param integer $index Optional index to insert the component into the array
     * @return boolean Whether the component was successfully added
     */
    public function add(GeometryInterface $component, $index = null)
    {
        $added = false;

        // Remove last point
        $lastPoint = array_pop($this->components);

        // Given an index, add the point,
        // without an index only add non-duplicate points
        if (null !== $index || !($lastPoint && $component->equals($lastPoint))) {
            $added = parent::add($component, $index);
        }

        // Append copy of first point
        $firstPoint = $this->components[0];
        parent::add($firstPoint);

        return $added;
    }

    /**
     * Note - The area is positive if the ring is oriented CW, otherwise
     * it will be negative.
     *
     * @return float The signed area for a ring
     */
    public function getArea()
    {
        $area = 0;
        if ($this->count() > 2) {
            $sum = 0;
            $components = $this->all();
            for ($i = 0, $len = count($components); $i < $len - 1; $i++) {
                $b = $components[$i];
                $c = $components[$i + 1];
                $sum += ($b->getX() + $c->getX()) * ($c->getY() - $b->getY());
            }
            $area = - ($sum / 2);
        }

        return $area;
    }
}
