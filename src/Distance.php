<?php

declare(strict_types=1);

namespace Geokit;

/**
 * Inspired by GeoPy's distance class (https://github.com/geopy/geopy)
 */
final class Distance
{
    public const UNIT_METERS = 'meters';
    public const UNIT_KILOMETERS = 'kilometers';
    public const UNIT_MILES = 'miles';
    public const UNIT_FEET = 'feet';
    public const UNIT_NAUTICAL = 'nautical';

    public const DEFAULT_UNIT = self::UNIT_METERS;

    private static $units = [
        self::UNIT_METERS => 1.0,
        self::UNIT_KILOMETERS => 1000.0,
        self::UNIT_MILES => 1609.344,
        self::UNIT_FEET => 0.3048,
        self::UNIT_NAUTICAL => 1852.0,
    ];

    private static $aliases = [
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
    ];

    private $value;

    public function __construct(float $value, string $unit = self::DEFAULT_UNIT)
    {
        if (!isset(self::$units[$unit])) {
            throw new \InvalidArgumentException(\sprintf('Invalid unit %s.', \json_encode($unit)));
        }

        $this->value = $value * self::$units[$unit];
    }

    public static function fromString(string $input): self
    {
        if (\preg_match('/(\-?\d+\.?\d*)\s*((kilo)?met[er]+s?|m|km|miles?|mi|feet|foot|ft|nautical(mile)?s?|nm)?$/', $input, $match)) {
            $unit = self::DEFAULT_UNIT;

            if (isset($match[2])) {
                $unit = $match[2];

                if (!isset(self::$units[$unit])) {
                    $unit = self::$aliases[$unit];
                }
            }

            return new self((float) $match[1], $unit);
        }

        throw new \InvalidArgumentException(
            \sprintf(
                'Cannot create Distance from string %s.',
                \json_encode($input)
            )
        );
    }

    public function meters(): float
    {
        return $this->value / self::$units[self::UNIT_METERS];
    }

    public function m(): float
    {
        return $this->meters();
    }

    public function kilometers(): float
    {
        return $this->value / self::$units[self::UNIT_KILOMETERS];
    }

    public function km(): float
    {
        return $this->kilometers();
    }

    public function miles(): float
    {
        return $this->value / self::$units[self::UNIT_MILES];
    }

    public function mi(): float
    {
        return $this->miles();
    }

    public function feet(): float
    {
        return $this->value / self::$units[self::UNIT_FEET];
    }

    public function ft(): float
    {
        return $this->feet();
    }

    public function nautical(): float
    {
        return $this->value / self::$units[self::UNIT_NAUTICAL];
    }

    public function nm(): float
    {
        return $this->nautical();
    }
}
