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

            throw new Exception\InvalidArgumentException(
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
            $lastPoint->latitude() === $firstPoint->latitude() &&
            $lastPoint->longitude() === $firstPoint->longitude()
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

        $x = $latLng->longitude();
        $y = $latLng->latitude();

        $p = \end($points);

        $x0 = $p->longitude();
        $y0 = $p->latitude();

        $inside = false;

        foreach ($points as $point) {
            $x1 = $point->longitude();
            $y1 = $point->latitude();

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
            throw new Exception\LogicException('Cannot create a BoundingBox from empty Polygon.');
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

    public function getIterator(): \Generator
    {
        yield from $this->points;
    }
}
