<?php

declare(strict_types=1);

namespace Geokit;

use Countable;
use Generator;
use IteratorAggregate;
use function array_shift;
use function count;
use function end;
use function json_encode;
use function reset;
use function sprintf;

final class Polygon implements Countable, IteratorAggregate
{
    /** @var array<Position> */
    private $positions;

    /**
     * @param array<Position> $positions
     */
    public function __construct(array $positions = [])
    {
        foreach ($positions as $index => $position) {
            /** @psalm-suppress RedundantConditionGivenDocblockType */
            if ($position instanceof Position) {
                continue;
            }

            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Position at index %s is not an instance of Geokit\Position.',
                    json_encode($index)
                )
            );
        }

        $this->positions = $positions;
    }

    public function isClosed() : bool
    {
        if (count($this->positions) === 0) {
            return false;
        }

        /** @var Position $lastPosition */
        $lastPosition = end($this->positions);
        /** @var Position $firstPosition */
        $firstPosition = reset($this->positions);

        return $lastPosition->latitude() === $firstPosition->latitude() &&
            $lastPosition->longitude() === $firstPosition->longitude();
    }

    public function close() : self
    {
        if (count($this->positions) === 0) {
            return new self();
        }

        $positions = $this->positions;

        if (!$this->isClosed()) {
            $positions[] = clone reset($this->positions);
        }

        $polygon            = new self();
        $polygon->positions = $positions;

        return $polygon;
    }

    /**
     * @see https://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html
     */
    public function contains(Position $position) : bool
    {
        if (count($this->positions) === 0) {
            return false;
        }

        $positions = $this->positions;

        $x = $position->longitude();
        $y = $position->latitude();

        /** @var Position $p */
        $p = end($positions);

        $x0 = $p->longitude();
        $y0 = $p->latitude();

        $inside = false;

        /** @var Position $pos */
        foreach ($positions as $pos) {
            $x1 = $pos->longitude();
            $y1 = $pos->latitude();

            if (($y1 > $y) !== ($y0 > $y) &&
                ($x < ($x0 - $x1) * ($y - $y1) / ($y0 - $y1) + $x1)
            ) {
                $inside = !$inside;
            }

            $x0 = $x1;
            $y0 = $y1;
        }

        return $inside;
    }

    public function toBoundingBox() : BoundingBox
    {
        if (count($this->positions) === 0) {
            throw new Exception\LogicException('Cannot create a BoundingBox from empty Polygon.');
        }

        $positions = $this->positions;

        /** @var Position $start */
        $start = array_shift($positions);

        $bbox = new BoundingBox($start, $start);

        /** @var Position $position */
        foreach ($positions as $position) {
            $bbox = $bbox->extend($position);
        }

        return $bbox;
    }

    public function count() : int
    {
        return count($this->positions);
    }

    public function getIterator() : Generator
    {
        yield from $this->positions;
    }
}
