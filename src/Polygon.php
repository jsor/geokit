<?php

declare(strict_types=1);

namespace Geokit;

use Countable;
use Generator;
use IteratorAggregate;
use function array_shift;
use function count;
use function end;
use function reset;
use function implode;

final class Polygon implements Countable, IteratorAggregate
{
    /** @var array<Position> */
    private $positions;

    private function __construct(Position ...$positions)
    {
        $this->positions = $positions;
    }

    public static function fromPositions(Position ...$positions): Polygon
    {
        return new self(...$positions);
    }

    /**
     * @param iterable<iterable<float>> $iterable
     */
    public static function fromCoordinates(iterable $iterable): Polygon
    {
        $positions = [];

        foreach ($iterable as $position) {
            $positions[] = Position::fromCoordinates($position);
        }

        return new self(...$positions);
    }

    public function isClosed(): ?bool
    {
        if (count($this->positions) === 0) {
            return null;
        }

        $positions = $this->positions;

        /** @var Position $lastPosition */
        $lastPosition = end($positions);

        /** @var Position $firstPosition */
        $firstPosition = reset($positions);

        return $lastPosition->latitude() === $firstPosition->latitude()
            && $lastPosition->longitude() === $firstPosition->longitude();
    }

    public function close(): Polygon
    {
        if (count($this->positions) === 0) {
            return new self();
        }

        if ($this->isClosed()===false) {
            $positions   = $this->positions;
            $positions[] = clone reset($this->positions);

            return new self(...$positions);
        }

        return $this;
    }

    /**
     * @see https://wrf.ecse.rpi.edu/Research/Short_Notes/pnpoly.html
     */
    public function contains(Position $position): bool
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

    public function toBoundingBox(): BoundingBox
    {
        if (count($this->positions) === 0) {
            throw new Exception\LogicException('Cannot create a BoundingBox from empty Polygon.');
        }

        $positions = $this->positions;

        /** @var Position $start */
        $start = array_shift($positions);

        $bbox = BoundingBox::fromCornerPositions($start, $start);

        /** @var Position $position */
        foreach ($positions as $position) {
            $bbox = $bbox->extend($position);
        }

        return $bbox;
    }

    public function count(): int
    {
        return count($this->positions);
    }

    public function getIterator(): Generator
    {
        yield from $this->positions;
    }

    public function toWKT(): string
    {
        if (count($this->positions) === 0) {
            return 'NULL';
        }

        $xyPairs = [];
        foreach ($this->positions as $position) {
            $xyPairs[] = $position->x() . ' ' . $position->y();
        }
        if ($this->isClosed()===false) {
            $position  = $this->positions[0];
            $xyPairs[] = $position->x() . ' ' . $position->y();
        }

        if (count($xyPairs) >= 3) return 'POLYGON((' . implode(', ', $xyPairs) . '))';

        return 'NULL';
    }
}
