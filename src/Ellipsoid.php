<?php

declare(strict_types=1);

namespace Geokit;

/**
 * Inspired by scalaz-geo's Ellipsoid (https://github.com/scalaz/scalaz-geo)
 *
 * @see http://en.wikipedia.org/wiki/Earth_ellipsoid
 */
final class Ellipsoid implements \ArrayAccess
{
    private $semiMajorAxis;
    private $semiMinorAxis;
    private $flattening;
    private $inverseFlattening;

    private static $semiMajorAxisKeys = array(
        'semi_major_axis',
        'semi_major',
        'semiMajorAxis',
        'semiMajor'
    );

    private static $semiMinorAxisKeys = array(
        'semi_minor_axis',
        'semi_minor',
        'semiMinorAxis',
        'semiMinor'
    );

    private static $flatteningKeys = array(
        'flattening',
        'f'
    );

    private static $inverseFlatteningKeys = array(
        'inverse_flattening',
        'inverse_f',
        'inv_f',
        'inverseFlattening',
        'inverseF',
        'invF'
    );

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
    )
    {
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

    public function offsetExists($offset): bool
    {
        return in_array(
            $offset,
            array_merge(
                self::$semiMajorAxisKeys,
                self::$semiMinorAxisKeys,
                self::$flatteningKeys,
                self::$inverseFlatteningKeys
            ),
            true
        );
    }

    public function offsetGet($offset)
    {
        if (in_array($offset, self::$semiMajorAxisKeys, true)) {
            return $this->getSemiMajorAxis();
        }

        if (in_array($offset, self::$semiMinorAxisKeys, true)) {
            return $this->getSemiMinorAxis();
        }

        if (in_array($offset, self::$flatteningKeys, true)) {
            return $this->getFlattening();
        }

        if (in_array($offset, self::$inverseFlatteningKeys, true)) {
            return $this->getInverseFlattening();
        }

        throw new \InvalidArgumentException(sprintf('Invalid offset %s.', json_encode($offset)));
    }

    public function offsetUnset($offset): void
    {
        throw new \BadMethodCallException('Ellipsoid is immutable.');
    }

    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException('Ellipsoid is immutable.');
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
    ): self
    {
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
    ): self
    {
        $flattening = ($semiMajorAxis - $semiMinorAxis) / $semiMajorAxis;

        return new self(
            $semiMajorAxis,
            $semiMinorAxis,
            $flattening,
            1 / $flattening
        );
    }

    /**
     * Takes anything which looks like an ellipsoid and generates an Ellipsoid
     * object from it.
     *
     * $input can be either an array, an \ArrayAccess object, an Ellipsoid
     * object or null.
     *
     * If $input is an array or \ArrayAccess object, it must have either a
     * semi-major axis and inverse flattening entry or an semi-majox axis and
     * semi-minor axis entry.
     *
     * Recognized keys are:
     *
     *  * Semi-majox axis:
     *    * semi_major_axis
     *    * semi_major
     *    * semiMajorAxis
     *    * semiMajor
     *
     *  * Semi-minor axis:
     *    * semi_minor_axis
     *    * semi_minor
     *    * semiMinorAxis
     *    * semiMinor
     *
     *  * Inverse flattening:
     *    * inverse_flattening
     *    * inverse_f
     *    * inv_f
     *    * inverseFlattening
     *    * inverseF
     *    * invF
     *
     * If $input is an indexed array, it assumes the semi-majox axis at index 0
     * and the inverse flattening at index 1, eg. [6378137.0, 298.257223563].
     *
     * If $input is an Ellipsoid object, it is just passed through.
     *
     * If $input is null, the default wgs84 ellipsoid is returned.
     *
     * @param mixed $input
     */
    public static function normalize($input): self
    {
        if ($input instanceof self) {
            return $input;
        }

        if (null === $input) {
            return self::wgs84();
        }

        $semiMajorAxis = null;
        $semiMinorAxis = null;
        $inverseFlattening = null;

        if (is_array($input) || $input instanceof \ArrayAccess) {
            if (Utils::isNumericInputArray($input)) {
                [$semiMajorAxis, $inverseFlattening] = $input;
            } else {
                $semiMajorAxis = Utils::extractFromInput($input, self::$semiMajorAxisKeys);
                $semiMinorAxis = Utils::extractFromInput($input, self::$semiMinorAxisKeys);
                $inverseFlattening = Utils::extractFromInput($input, self::$inverseFlatteningKeys);
            }
        }

        if (is_numeric($semiMajorAxis) && is_numeric($inverseFlattening)) {
            return self::createFromSemiMajorAndInvF($semiMajorAxis, $inverseFlattening);
        }

        if (is_numeric($semiMajorAxis) && is_numeric($semiMinorAxis)) {
            return self::createFromSemiMajorAndSemiMinor($semiMajorAxis, $semiMinorAxis);
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize Ellipsoid from input %s.', json_encode($input)));
    }
}
