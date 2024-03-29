<?php

declare(strict_types=1);

namespace Geokit;

use Countable;
use Generator;
use IteratorAggregate;
use JsonSerializable;
use function array_shift;
use function count;
use function end;
use function reset;

final class Polygon implements Countable, IteratorAggregate, JsonSerializable
{
    /** @var array<Position> */
    private $positions;

    private function __construct(Position ...$positions)
    {
        $this->positions = $positions;
    }

    public static function fromPositions(Position ...$positions): self
    {
        return new self(...$positions);
    }

    /**
     * @param iterable<iterable<float>> $iterable
     */
    public static function fromCoordinates(iterable $iterable): self
    {
        $positions = [];

        foreach ($iterable as $position) {
            $positions[] = Position::fromCoordinates($position);
        }

        return new self(...$positions);
    }

    public function close(): self
    {
        if (0 === count($this->positions)) {
            return new self();
        }

        $positions = $this->positions;

        $lastPosition = end($positions);
        $firstPosition = reset($positions);

        $isClosed = (
            $lastPosition->latitude() === $firstPosition->latitude() &&
            $lastPosition->longitude() === $firstPosition->longitude()
        );

        if (!$isClosed) {
            $positions[] = clone reset($this->positions);
        }

        return new self(...$positions);
    }

    /**
     * @see https://wrf.ecse.rpi.edu/Research/Short_Notes/pnpoly.html
     */
    public function contains(Position $position): bool
    {
        if (0 === count($this->positions)) {
            return false;
        }

        $positions = $this->positions;

        $x = $position->longitude();
        $y = $position->latitude();

        $p = end($positions);

        $x0 = $p->longitude();
        $y0 = $p->latitude();

        $inside = false;

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
        if (0 === count($this->positions)) {
            throw new Exception\LogicException('Cannot create a BoundingBox from empty Polygon.');
        }

        $positions = $this->positions;

        $start = array_shift($positions);

        $bbox = BoundingBox::fromCornerPositions($start, $start);

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

    /**
     * @return iterable<iterable<float>>
     */
    public function toCoordinates(): iterable
    {
        foreach ($this->positions as $position) {
            yield $position->toCoordinates();
        }
    }

    /**
     * @return array<array<float>>
     */
    public function jsonSerialize(): array
    {
        $coordinates = [];

        foreach ($this->positions as $position) {
            $coordinates[] = $position->jsonSerialize();
        }

        return $coordinates;
    }
}
