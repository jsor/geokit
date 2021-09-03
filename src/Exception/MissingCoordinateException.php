<?php

declare(strict_types=1);

namespace Geokit\Exception;

use function sprintf;

final class MissingCoordinateException extends InvalidArgumentException
{
    public static function create(
        string $coordinate,
        int $position
    ): self {
        return new self(
            sprintf(
                'Missing %s-coordinate at position %d.',
                $coordinate,
                $position
            )
        );
    }
}
