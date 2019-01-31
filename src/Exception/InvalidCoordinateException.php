<?php

declare(strict_types=1);

namespace Geokit\Exception;

final class InvalidCoordinateException extends InvalidArgumentException
{
    public static function create(string $coordinate, $given): self
    {
        return new self(
            \sprintf(
                'The %s-coordinate must be float, %s given.',
                $coordinate,
                \gettype($given)
            )
        );
    }
}
