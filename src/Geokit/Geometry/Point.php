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
class Point extends Geometry
{
    /**
     * @var float
     */
    private $x;

    /**
     * @var float
     */
    private $y;

    /**
     * @param float $x
     * @param float $y
     */
    public function __construct($x, $y)
    {
        $this->x = (float) $x;
        $this->y = (float) $y;
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * {@inheritDoc}
     */
    public function equals(GeometryInterface $geometry)
    {
        if ($this->getGeometryType() !== $geometry->getGeometryType()) {
            return false;
        }

        return $this->getX() === $geometry->getX() &&
               $this->getY() === $geometry->getY();
    }

    /**
     * {@inheritDoc}
     */
    public function getCentroid()
    {
        return clone $this;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return array($this->getX(), $this->getY());
    }
}
