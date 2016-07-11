<?php

namespace Geokit;

class Polygon implements \Countable, \ArrayAccess, \IteratorAggregate
{
    private $points;

    public function __construct(array $points = array())
    {
        $this->points = array_map(function ($latLng) {
            return LatLng::normalize($latLng);
        }, $points);
    }

    public function isClosed()
    {
        if (0 === count($this->points)) {
            return false;
        }

        $lastPoint  = end($this->points);
        $firstPoint = reset($this->points);

        return (
            $lastPoint->getLatitude() === $firstPoint->getLatitude() &&
            $lastPoint->getLongitude() === $firstPoint->getLongitude()
        );
    }

    public function close()
    {
        if (0 === count($this->points)) {
            return new self();
        }

        $points = $this->points;

        if (!$this->isClosed()) {
            $points[] = clone reset($this->points);
        }

        return new self($points);
    }

    /**
     * @see https://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html
     * @param  LatLng  $latLng
     * @return boolean
     */
    public function contains(LatLng $latLng)
    {
        if (0 === count($this->points)) {
            return false;
        }

        $points = $this->points;

        $x = $latLng->getLongitude();
        $y = $latLng->getLatitude();

        $p = end($points);

        $x0 = $p->getLongitude();
        $y0 = $p->getLatitude();

        $inside = false;

        foreach ($points as $point) {
            $x1 = $point->getLongitude();
            $y1 = $point->getLatitude();

            if (
                (($y1 > $y) !== ($y0 > $y)) &&
                ($x < ($x0 - $x1) * ($y - $y1) / ($y0 - $y1) + $x1)
            ) {
                $inside = !$inside;
            }

            $x0 = $x1;
            $y0 = $y1;
        }

        return $inside;
    }

    public function toBounds()
    {
        if (0 === count($this->points)) {
            throw new \LogicException('Cannot create Bounds from empty Polygon.');
        }

        $points = $this->points;
        $start = array_shift($points);

        $bounds = new Bounds($start, $start);

        foreach ($points as $latLng) {
            $bounds = $bounds->extend($latLng);
        }

        return $bounds;
    }

    public function count()
    {
        return count($this->points);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->points);
    }

    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->points[$offset];
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Invalid offset %s.',
                json_encode($offset)
            )
        );
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Polygon is immutable.');
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Polygon is immutable.');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->points);
    }
}
