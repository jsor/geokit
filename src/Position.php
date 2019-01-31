<?php

declare(strict_types=1);

namespace Geokit;

use Geokit\Exception\InvalidCoordinateException;
use Geokit\Exception\MissingCoordinateException;

final class Position implements \JsonSerializable
{
    private $x;
    private $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function fromCoordinates(iterable $iterable): Position
    {
        $array = [];

        foreach ($iterable as $coordinate) {
            $array[] = $coordinate;

            if (isset($array[1])) {
                break;
            }
        }

        if (!\array_key_exists(0, $array)) {
            throw MissingCoordinateException::create('x', 0);
        }

        if (!\array_key_exists(1, $array)) {
            throw MissingCoordinateException::create('y', 1);
        }

        if (!\is_int($array[0]) && !\is_float($array[0])) {
            throw InvalidCoordinateException::create('x', $array[0]);
        }

        if (!\is_int($array[1]) && !\is_float($array[1])) {
            throw InvalidCoordinateException::create('y', $array[1]);
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
        return Utils::normalizeLng($this->x);
    }

    public function latitude(): float
    {
        return Utils::normalizeLat($this->y);
    }

    public function toCoordinates(): iterable
    {
        return [$this->x, $this->y];
    }

    public function jsonSerialize(): array
    {
        return [$this->x, $this->y];
    }
}
