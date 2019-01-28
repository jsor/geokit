<?php

declare(strict_types=1);

namespace Geokit;

final class Polygon implements \Countable, \IteratorAggregate
{
    private $points;

    /**
     * @param LatLng[] $points
     */
    public function __construct(array $points = [])
    {
        \array_walk($points, static function ($latLng, $index) {
            if ($latLng instanceof LatLng) {
                return;
            }

            throw new \InvalidArgumentException(
                \sprintf(
                    'Point at index %s is not an instance of Geokit\LatLng.',
                    \json_encode($index)
                )
            );
        });

        $this->points = $points;
    }

    public function isClosed(): bool
    {
        if (0 === \count($this->points)) {
            return false;
        }

        $lastPoint = \end($this->points);
        $firstPoint = \reset($this->points);

        return (
            $lastPoint->getLatitude() === $firstPoint->getLatitude() &&
            $lastPoint->getLongitude() === $firstPoint->getLongitude()
        );
    }

    public function close(): self
    {
        if (0 === \count($this->points)) {
            return new self();
        }

        $points = $this->points;

        if (!$this->isClosed()) {
            $points[] = clone \reset($this->points);
        }

        $polygon = new self();
        $polygon->points = $points;

        return $polygon;
    }

    /**
     * @see https://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html
     */
    public function contains(LatLng $latLng): bool
    {
        if (0 === \count($this->points)) {
            return false;
        }

        $points = $this->points;

        $x = $latLng->getLongitude();
        $y = $latLng->getLatitude();

        $p = \end($points);

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

    public function toBoundingBox(): BoundingBox
    {
        if (0 === \count($this->points)) {
            throw new \LogicException('Cannot create a BoundingBox from empty Polygon.');
        }

        $points = $this->points;
        $start = \array_shift($points);

        $bbox = new BoundingBox($start, $start);

        foreach ($points as $latLng) {
            $bbox = $bbox->extend($latLng);
        }

        return $bbox;
    }

    public function count(): int
    {
        return \count($this->points);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->points);
    }
}
