<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit;

/**
 * Inspired by GeoPy's distance class (https://github.com/geopy/geopy)
 */
class Distance
{
    const DEFAULT_UNIT = 'meters';

    private static $units = array(
        'meters' => 1.0,
        'kilometers' => 1000.0,
        'miles' => 1609.344,
        'feet' => 0.3048,
        'nautical' => 1852.0,
    );

    private $value;

    public function __construct($value, $unit = self::DEFAULT_UNIT)
    {
        if (!isset(self::$units[$unit])) {
            throw new \InvalidArgumentException(sprintf('Unknown unit type %s.', json_encode($unit)));
        }

        $this->value = $value * self::$units[$unit];
    }

    /**
     * @return float
     */
    public function meters()
    {
        return $this->value / self::$units['meters'];
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
        return $this->value / self::$units['kilometers'];
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
        return $this->value / self::$units['miles'];
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
        return $this->value / self::$units['feet'];
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
        return $this->value / self::$units['nautical'];
    }

    /**
     * @return float
     */
    public function nm()
    {
        return $this->nautical();
    }
}
