<?php

declare(strict_types=1);

namespace Geokit;

use Geokit\Exception\MissingCoordinateException;
use JsonSerializable;
use function array_key_exists;

final class Position implements JsonSerializable
{
    /** @var float */
    private $x;

    /** @var float */
    private $y;

    private function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function fromXY(float $x, float $y): Position
    {
        return new self($x, $y);
    }

    /**
     * @param iterable<float> $iterable
     */
    public static function fromCoordinates(iterable $iterable): Position
    {
        $array = [];

        foreach ($iterable as $coordinate) {
            $array[] = $coordinate;

            if (isset($array[1])) {
                break;
            }
        }

        if (!array_key_exists(0, $array)) {
            throw MissingCoordinateException::create('x', 0);
        }

        if (!array_key_exists(1, $array)) {
            throw MissingCoordinateException::create('y', 1);
        }

        return new self($array[0], $array[1]);
    }

    public function x(): float
    {
        return $this->x;
    }

    public function y(): float
    {
        return $this->y;
    }

    public function longitude(): float
    {
        return normalizeLongitude($this->x);
    }

    public function latitude(): float
    {
        return normalizeLatitude($this->y);
    }

    /**
     * @return iterable<float>
     */
    public function toCoordinates(): iterable
    {
        return [$this->x, $this->y];
    }

    /**
     * @return array<float>
     */
    public function jsonSerialize(): array
    {
        return [$this->x, $this->y];
    }

    public function toWKT(): string
    {
        return 'POINT(' . $this->x . ' ' . $this->y . ')';
    }
}
