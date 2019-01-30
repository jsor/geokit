<?php

declare(strict_types=1);

namespace Geokit;

final class Earth
{
    /**
     * Mean earth radius
     * @see https://en.wikipedia.org/wiki/Earth_radius#Mean_radius
     */
    public const RADIUS = 6371008.8;

    public const SEMI_MAJOR_AXIS = 6378137.0;

    public const SEMI_MINOR_AXIS = 6356752.3142;

    public const INVERSE_FLATTENING = 298.257223563;

    public const FLATTENING = 1 / self::INVERSE_FLATTENING;
}
