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
    private $southWest;
    private $northEast;

    private static $southWestKeys = array(
        'southwest',
        'south_west',
        'southWest'
    );

    private static $northEastKeys = array(
        'northeast',
        'north_east',
        'northEast'
    );

    /**
     * @param  \Geokit\LatLng  $southWest
     * @param  \Geokit\LatLng  $northEast
     * @throws \LogicException
     */
    public function __construct(LatLng $southWest, LatLng $northEast)
    {
        $this->southWest = $southWest;
        $this->northEast = $northEast;

        if ($this->southWest->getLatitude() > $this->northEast->getLatitude()) {
            throw new \LogicException('Bounds south-west coordinate cannot be north of the east-north coordinate');
        }
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getSouthWest()
    {
        return $this->southWest;
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getNorthEast()
    {
        return $this->northEast;
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getCenter()
    {
        if ($this->crossesAntimeridian()) {
            $span = $this->lngSpan($this->southWest->getLongitude(), $this->northEast->getLongitude());
            $lng  = $this->southWest->getLongitude() + $span / 2;
        } else {
            $lng = ($this->southWest->getLongitude() + $this->northEast->getLongitude()) / 2;
        }

        return new LatLng(
            ($this->southWest->getLatitude() + $this->northEast->getLatitude()) / 2,
            $lng
        );
    }

    /**
     * @return \Geokit\LatLng
     */
    public function getSpan()
    {
        return new LatLng(
            $this->northEast->getLatitude() - $this->southWest->getLatitude(),
            $this->lngSpan($this->southWest->getLongitude(), $this->northEast->getLongitude())
        );
    }

    public function offsetExists($offset)
    {
        return in_array($offset, self::$southWestKeys) ||
               in_array($offset, self::$northEastKeys) ||
               in_array($offset, array('center', 'span'));
    }

    public function offsetGet($offset)
    {
        if (in_array($offset, self::$southWestKeys)) {
            return $this->getSouthWest();
        }

        if (in_array($offset, self::$northEastKeys)) {
            return $this->getNorthEast();
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
        return $this->southWest->getLongitude() > $this->northEast->getLongitude();
    }

    /**
     * @param  \Geokit\LatLng $latLng
     * @return boolean
     */
    public function contains(LatLng $latLng)
    {
      // check latitude
      if ($this->southWest->getLatitude() > $latLng->getLatitude() ||
          $latLng->getLatitude() > $this->northEast->getLatitude()) {
          return false;
      }

      // check longitude
      return $this->containsLng($latLng->getLongitude());
    }

    /**
     * @param  LatLng $latLng
     * @return Bounds
     */
    public function extend(LatLng $latLng)
    {
        $newSouth = min($this->southWest->getLatitude(), $latLng->getLatitude());
        $newNorth = max($this->northEast->getLatitude(), $latLng->getLatitude());

        $newWest = $this->southWest->getLongitude();
        $newEast = $this->northEast->getLongitude();

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

        return new self(new LatLng($newSouth, $newWest), new LatLng($newNorth, $newEast));
    }

    /**
     * @param  Bounds $bounds
     * @return Bounds
     */
    public function union(Bounds $bounds)
    {
        $newBounds = $this->extend($bounds->getSouthWest());

        return $newBounds->extend($bounds->getNorthEast());
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
            return $lng <= $this->northEast->getLongitude() ||
                   $lng >= $this->southWest->getLongitude();
        } else {
            return $this->southWest->getLongitude() <= $lng &&
                   $lng <= $this->northEast->getLongitude();
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
     * If $input is an array or \ArrayAccess object, it must have a south-west
     * and east-north entry.
     *
     * Recognized keys are:
     *
     *  * South-west:
     *    * southwest
     *    * south_west
     *    * southWest
     *
     *  * North-east:
     *    * northeast
     *    * north_east
     *    * northEast
     *
     * If $input is an indexed array, it assumes south-west at index 0 and
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

        $southWest = null;
        $northEast = null;

        if (is_string($input) && preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $input, $match)) {
            $southWest = array('lng' => $match[1], 'lat' => $match[2]);
            $northEast = array('lng' => $match[3], 'lat' => $match[4]);
        } elseif (is_array($input) || $input instanceof \ArrayAccess) {
            $southWest = self::extract($input, self::$southWestKeys);

            if (!$southWest && isset($input[1])) {
                $southWest = $input[0];
            }

            $northEast = self::extract($input, self::$northEastKeys);

            if (!$northEast && isset($input[0])) {
                $northEast = $input[1];
            }
        }

        if (null !== $southWest && null !== $northEast) {
            try {
                return new self(LatLng::normalize($southWest), LatLng::normalize($northEast));
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
