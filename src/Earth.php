<?php

declare(strict_types=1);

namespace Geokit;

/**
 * @see http://en.wikipedia.org/wiki/World_Geodetic_System
 * @see https://en.wikipedia.org/wiki/Earth_radius
 */
final class Earth
{
    public const SEMI_MAJOR_AXIS = 6378137.0;

    public const INVERSE_FLATTENING = 298.257223563;

    /**
     * SEMI_MAJOR_AXIS - SEMI_MAJOR_AXIS / INVERSE_FLATTENING.
     */
    public const SEMI_MINOR_AXIS = 6356752.3142;

    /**
     * 1 / INVERSE_FLATTENING.
     */
    public const FLATTENING = 0.0033528106647475;

    /**
     * Mean earth radius.
     *
     * (2 * SEMI_MAJOR_AXIS + SEMI_MINOR_AXIS) / 3
     *
     * @see https://en.wikipedia.org/wiki/Earth_radius#Mean_radius
     */
    public const RADIUS = 6371008.8;
}
