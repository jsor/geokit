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

class Bounds implements \ArrayAccess
{
    private $westSouth;
    private $eastNorth;

    private static $westSouthKeys = array(
        'westsouth',
        'west_south',
        'westSouth'
    );

    private static $eastNorthKeys = array(
        'eastnorth',
        'east_north',
        'eastNorth'
    );

    /**
     * @param \Geokit\LngLat $westSouth
     * @param \Geokit\LngLat $eastNorth
     * @throws \LogicException
     */
    public function __construct(LngLat $westSouth, LngLat $eastNorth)
    {
        $this->westSouth = $westSouth;
        $this->eastNorth = $eastNorth;

        if ($this->westSouth->getLatitude() > $this->eastNorth->getLatitude()) {
            throw new \LogicException('Bounds west-south coordinate cannot be north of the east-north coordinate');
        }
    }

    /**
     * @return \Geokit\LngLat
     */
    public function getWestSouth()
    {
        return $this->westSouth;
    }

    /**
     * @return \Geokit\LngLat
     */
    public function getEastNorth()
    {
        return $this->eastNorth;
    }

    /**
     * @return \Geokit\LngLat
     */
    public function getCenter()
    {
        if ($this->crossesAntimeridian()) {
            $span = $this->lngSpan($this->westSouth->getLongitude(), $this->eastNorth->getLongitude());
            $lng  = Calc::normalizeLng($this->westSouth->getLongitude() + $span / 2);
        } else {
            $lng = ($this->westSouth->getLongitude() + $this->eastNorth->getLongitude()) / 2;
        }

        return new LngLat(
            $lng,
            ($this->westSouth->getLatitude() + $this->eastNorth->getLatitude()) / 2
        );
    }

    /**
     * @return \Geokit\LngLat
     */
    public function getSpan()
    {
        return new LngLat(
            $this->lngSpan($this->westSouth->getLongitude(), $this->eastNorth->getLongitude()),
            $this->eastNorth->getLatitude() - $this->westSouth->getLatitude()
        );
    }

    public function offsetExists($offset)
    {
        return in_array($offset, self::$westSouthKeys) ||
               in_array($offset, self::$eastNorthKeys) ||
               in_array($offset, array('center', 'span'));
    }

    public function offsetGet($offset)
    {
        if (in_array($offset, self::$westSouthKeys)) {
            return $this->getWestSouth();
        }

        if (in_array($offset, self::$eastNorthKeys)) {
            return $this->getEastNorth();
        }

        if ('center' === $offset) {
            return $this->getCenter();
        }

        if ('span' === $offset) {
            return $this->getSpan();
        }

        throw new \InvalidArgumentException(sprintf('Invalid offset %s.', json_encode($offset)));
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Bounds is immutable.');
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Bounds is immutable.');
    }

    /**
     * @return boolean
     */
    public function crossesAntimeridian()
    {
        return $this->westSouth->getLongitude() > $this->eastNorth->getLongitude();
    }

    /**
     * @param  \Geokit\LngLat $latLng
     * @return boolean
     */
    public function containsLngLat(LngLat $latLng)
    {
      // check latitude
      if ($this->westSouth->getLatitude() > $latLng->getLatitude() ||
          $latLng->getLatitude() > $this->eastNorth->getLatitude()) {
          return false;
      }

      // check longitude
      return $this->containsLng($latLng->getLongitude());
    }

    /**
     * @param  LngLat $latLng
     * @return Bounds
     */
    public function extendByLngLat(LngLat $latLng)
    {
        $newSouth = min($this->westSouth->getLatitude(), $latLng->getLatitude());
        $newNorth = max($this->eastNorth->getLatitude(), $latLng->getLatitude());

        $newWest = $this->westSouth->getLongitude();
        $newEast = $this->eastNorth->getLongitude();

        if (!$this->containsLng($latLng->getLongitude())) {
            // try extending east and try extending west, and use the one that
            // has the smaller longitudinal span
            $extendEastLngSpan = $this->lngSpan($newWest, $latLng->getLongitude());
            $extendWestLngSpan = $this->lngSpan($latLng->getLongitude(), $newEast);

            if ($extendEastLngSpan <= $extendWestLngSpan) {
                $newEast = $latLng->getLongitude();
            } else {
                $newWest = $latLng->getLongitude();
            }
        }

        return new self(new LngLat($newWest, $newSouth), new LngLat($newEast, $newNorth));
    }

    /**
     * @param  Bounds $bounds
     * @return Bounds
     */
    public function extendByBounds(Bounds $bounds)
    {
        $newBounds = $this->extendByLngLat($bounds->getWestSouth());

        return $newBounds->extendByLngLat($bounds->getEastNorth());
    }

    /**
     * Returns whether or not the given line of longitude is inside the bounds.
     *
     * @param  float   $lng
     * @return boolean
     */
    protected function containsLng($lng)
    {
        if ($this->crossesAntimeridian()) {
            return $lng <= $this->eastNorth->getLongitude() ||
                   $lng >= $this->westSouth->getLongitude();
        } else {
            return $this->westSouth->getLongitude() <= $lng &&
                   $lng <= $this->eastNorth->getLongitude();
        }
    }

    /**
     * Gets the longitudinal span of the given west and east coordinates.
     *
     * @param  float $west
     * @param  float $east
     * @return float
     */
    protected function lngSpan($west, $east)
    {
        return ($west > $east) ? ($east + 360 - $west) : ($east - $west);
    }

    /**
     * Takes anything which looks like bounds and generates a Bounds object
     * from it.
     *
     * $input can be either a string, an array, an \ArrayAccess object or a
     * Bounds object.
     *
     * If $input is a string, it can be in the format
     * "1.1234, 2.5678, 3.910, 4.1112" or "1.1234 2.5678 3.910 4.1112".
     *
     * If $input is an array or \ArrayAccess object, it must have a west-south
     * and east-north entry.
     *
     * Recognized keys are:
     *
     *  * West-south:
     *    * westsouth
     *    * west_south
     *    * westSouth
     *
     *  * East-north:
     *    * eastnorth
     *    * east_north
     *    * eastNorth
     *
     * If $input is an indexed array, it assumes west-south at index 0 and
     * east-north at index 1, eg. [[180.0, -45.0], [-180.0, 45.0]].
     *
     * If $input is an Bounds object, it is just passed through.
     *
     * @param  string|array|\ArrayAccess|\Geokit\Bounds $input
     * @return \Geokit\Bounds
     * @throws \InvalidArgumentException
     */
    public static function normalize($input)
    {
        if ($input instanceof self) {
            return $input;
        }

        $westSouth = null;
        $eastNorth = null;

        if (is_string($input) && preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $input, $match)) {
            $westSouth = array('lng' => $match[1], 'lat' => $match[2]);
            $eastNorth = array('lng' => $match[3], 'lat' => $match[4]);
        } elseif (is_array($input) || $input instanceof \ArrayAccess) {
            $westSouth = self::extract($input, self::$westSouthKeys);

            if (!$westSouth && isset($input[1])) {
                $westSouth = $input[0];
            }

            $eastNorth = self::extract($input, self::$eastNorthKeys);

            if (!$eastNorth && isset($input[0])) {
                $eastNorth = $input[1];
            }
        }

        if (null !== $westSouth && null !== $eastNorth) {
            try {
                return new self(LngLat::normalize($westSouth), LngLat::normalize($eastNorth));
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException(sprintf('Cannot normalize Bounds from input %s.', json_encode($input)), 0, $e);
            }
        }

        throw new \InvalidArgumentException(sprintf('Cannot normalize Bounds from input %s.', json_encode($input)));
    }

    private static function extract($input, $keys)
    {
        foreach ($keys as $key) {
            if (!isset($input[$key])) {
                continue;
            }

            return $input[$key];
        }

        return null;
    }
}
