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

use Geokit\Bounds;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class GeometryCollection extends Geometry implements GeometryCollectionInterface, \Countable
{
    /**
     * @var array
     */
    protected $components = array();

    /**
     * An array of class names representing the types of
     * components that the collection can include. A null value means the
     * component types are not restricted.
     *
     * @var array
     */
    protected $componentGeometryTypes = null;

    /**
     * Constructor.
     *
     * @param array $components The components array
     */
    public function __construct(array $components = array())
    {
        foreach ($components as $component) {
            $this->add($component);
        }
    }

    /**
     * Add a component.
     *
     * @param GeometryInterface $component
     * @param integer $index Optional index to insert the component into the array
     * @return boolean Whether the component was successfully added
     */
    public function add(GeometryInterface $component, $index = null)
    {
        if ($this->componentGeometryTypes !== null &&
            !in_array($component->getGeometryType(), $this->componentGeometryTypes)) {

            return false;
        }

        $length = count($this->components);

        if (null !== $index && $index < $length) {
            $components1 = array_slice($this->components, 0, $index);
            $components2 = array_slice($this->components, $index, $length);
            $components1[] = $component;
            $this->components = array_merge($components1, $components2);
        } else {
            $this->components[] = $component;
        }

        return true;
    }

    /**
     * Returns all components.
     *
     * @return array
     */
    public function all()
    {
        return $this->components;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(GeometryInterface $geometry)
    {
        if ($this->getGeometryType() !== $geometry->getGeometryType()) {
            return false;
        }

        if ($this->count() !== $geometry->count()) {
            return false;
        }

        foreach ($geometry->all() as $index => $component) {
            if (!$component->equals($this->components[$index])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the area of this geometry. Note how this function is overridden
     * in Polygon.
     *
     * @return float The area of the collection by summing its parts
     */
    public function getArea()
    {
        $area = 0;
        foreach ($this->all() as $component) {
            if ($component instanceof GeometryCollectionInterface) {
                $area += $component->getArea();
            }
        }

        return $area;
    }

    /**
     * {@inheritDoc}
     */
    public function getCentroid()
    {
        // @Todo
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $array = array();

        foreach ($this->all() as $component) {
            $array[] = $component->toArray();
        }

        return $array;
    }

    /**
     * Implements Countable Interface.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->components);
    }
}
