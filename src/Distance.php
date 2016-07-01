<?php

namespace Geokit;

/**
 * Inspired by GeoPy's distance class (https://github.com/geopy/geopy)
 */
class Distance
{
    const UNIT_METERS = 'meters';
    const UNIT_KILOMETERS = 'kilometers';
    const UNIT_MILES = 'miles';
    const UNIT_FEET = 'feet';
    const UNIT_NAUTICAL = 'nautical';

    const DEFAULT_UNIT = self::UNIT_METERS;

    private static $units = array(
        self::UNIT_METERS => 1.0,
        self::UNIT_KILOMETERS => 1000.0,
        self::UNIT_MILES => 1609.344,
        self::UNIT_FEET => 0.3048,
        self::UNIT_NAUTICAL => 1852.0,
    );

    private static $aliases = array(
        'meter' => self::UNIT_METERS,
        'metre' => self::UNIT_METERS,
        'metres' => self::UNIT_METERS,
        'm' => self::UNIT_METERS,
        'kilometer' => self::UNIT_KILOMETERS,
        'kilometre' => self::UNIT_KILOMETERS,
        'kilometres' => self::UNIT_KILOMETERS,
        'km' => self::UNIT_KILOMETERS,
        'mile' => self::UNIT_MILES,
        'mi' => self::UNIT_MILES,
        'foot' => self::UNIT_FEET,
        'ft' => self::UNIT_FEET,
        'nm' => self::UNIT_NAUTICAL,
        'nauticalmile' => self::UNIT_NAUTICAL,
        'nauticalmiles' => self::UNIT_NAUTICAL,
    );

    private $value;

    /**
     * @param  integer|float             $value
     * @param  string                    $unit
     * @throws \InvalidArgumentException
     */
    public function __construct($value, $unit = self::DEFAULT_UNIT)
    {
        if (!isset(self::$units[$unit])) {
            throw new \InvalidArgumentException(sprintf('Invalid unit %s.', json_encode($unit)));
        }

        $this->value = $value * self::$units[$unit];
    }

    /**
     * @return float
     */
    public function meters()
    {
        return $this->value / self::$units[self::UNIT_METERS];
    }

    /**
     * @return float
     */
    public function m()
    {
        return $this->meters();
    }

    /**
     * @return float
     */
    public function kilometers()
    {
        return $this->value / self::$units[self::UNIT_KILOMETERS];
    }

    /**
     * @return float
     */
    public function km()
    {
        return $this->kilometers();
    }

    /**
     * @return float
     */
    public function miles()
    {
        return $this->value / self::$units[self::UNIT_MILES];
    }

    /**
     * @return float
     */
    public function mi()
    {
        return $this->miles();
    }

    /**
     * @return float
     */
    public function feet()
    {
        return $this->value / self::$units[self::UNIT_FEET];
    }

    /**
     * @return float
     */
    public function ft()
    {
        return $this->feet();
    }

    /**
     * @return float
     */
    public function nautical()
    {
        return $this->value / self::$units[self::UNIT_NAUTICAL];
    }

    /**
     * @return float
     */
    public function nm()
    {
        return $this->nautical();
    }

    /**
     * Takes anything which looks like a distance and generates a Distance
     * object from it.
     *
     * $input can be either a string, a float/integer or a Distance object.
     *
     * If $input is a string, it can be a number followed by a unit, eg. "100m".
     *
     * If $input is a float/integer, it uses the default unit (meters).
     *
     * If $input is a Distance object, it is just passed through.
     *
     * @param  mixed                     $input
     * @return Distance
     * @throws \InvalidArgumentException
     */
    public static function normalize($input)
    {
        if ($input instanceof self) {
            return $input;
        }

        if (is_numeric($input)) {
            return new self($input);
        }

        if (is_string($input) && preg_match('/(\-?\d+\.?\d*)\s*((kilo)?met[er]+s?|m|km|miles?|mi|feet|foot|ft|nautical(mile)?s?|nm)$/', $input, $match)) {
            $unit = $match[2];

            if (!isset(self::$units[$unit])) {
                $unit = self::resolveUnitAlias($unit);
            }

            return new self((float) $match[1], $unit);
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize Distance from input %s.', json_encode($input)));
    }

    /**
     * @param  string                    $alias
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function resolveUnitAlias($alias)
    {
        if (isset(self::$aliases[$alias])) {
            return self::$aliases[$alias];
        }

        throw new \InvalidArgumentException(sprintf('Cannot resolve unit alias %s.', json_encode($alias)));
    }
}
