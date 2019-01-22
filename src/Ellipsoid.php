<?php

declare(strict_types=1);

namespace Geokit;

/**
 * Inspired by scalaz-geo's Ellipsoid (https://github.com/scalaz/scalaz-geo)
 *
 * @see http://en.wikipedia.org/wiki/Earth_ellipsoid
 */
final class Ellipsoid
{
    private $semiMajorAxis;
    private $semiMinorAxis;
    private $flattening;
    private $inverseFlattening;

    /**
     * @param float $semiMajorAxis
     * @param float $semiMinorAxis
     * @param float $flattening
     * @param float $inverseFlattening
     */
    public function __construct(
        float $semiMajorAxis,
        float $semiMinorAxis,
        float $flattening,
        float $inverseFlattening
    ) {
        $this->semiMajorAxis = $semiMajorAxis;
        $this->semiMinorAxis = $semiMinorAxis;
        $this->flattening = $flattening;
        $this->inverseFlattening = $inverseFlattening;
    }

    public function getSemiMajorAxis(): float
    {
        return $this->semiMajorAxis;
    }

    public function getSemiMinorAxis(): float
    {
        return $this->semiMinorAxis;
    }

    public function getFlattening(): float
    {
        return $this->flattening;
    }

    public function getInverseFlattening(): float
    {
        return $this->inverseFlattening;
    }

    /**
     * World Geodetic System 1984
     */
    public static function wgs84(): self
    {
        return self::createFromSemiMajorAndInvF(
            6378137.0,
            298.257223563
        );
    }

    /**
     * World Geodetic System 1972
     */
    public static function wgs72(): self
    {
        return self::createFromSemiMajorAndInvF(
            6378135.0,
            298.26
        );
    }

    /**
     * World Geodetic System 1966
     */
    public static function wgs66(): self
    {
        return self::createFromSemiMajorAndInvF(
            6378145.0,
            298.25
        );
    }

    /**
     * Geodetic Reference System 1980
     */
    public static function grs80(): self
    {
        return self::createFromSemiMajorAndInvF(
            6378137.0,
            298.257222101
        );
    }

    /**
     * Australian National Spheroid
     */
    public static function ans(): self
    {
        return self::createFromSemiMajorAndInvF(
            6378160.0,
            298.25
        );
    }

    /**
     * Airy 1830
     */
    public static function airy1830(): self
    {
        return self::createFromSemiMajorAndInvF(
            6377563.396,
            299.3249646
        );
    }

    /**
     * Krassovsky 1940
     */
    public static function krassovsky1940(): self
    {
        return self::createFromSemiMajorAndInvF(
            6378245.0,
            298.3
        );
    }

    public static function createFromSemiMajorAndInvF(
        float $semiMajorAxis,
        float $inverseFlattening
    ): self {
        if ($inverseFlattening <= 0) {
            throw new \InvalidArgumentException('The inverse flattening must be > 0.');
        }

        $flattening = 1 / $inverseFlattening;

        return new self(
            $semiMajorAxis,
            (1 - $flattening) * $semiMajorAxis,
            $flattening,
            $inverseFlattening
        );
    }

    public static function createFromSemiMajorAndSemiMinor(
        float $semiMajorAxis,
        float $semiMinorAxis
    ): self {
        $flattening = ($semiMajorAxis - $semiMinorAxis) / $semiMajorAxis;

        return new self(
            $semiMajorAxis,
            $semiMinorAxis,
            $flattening,
            1 / $flattening
        );
    }
}
