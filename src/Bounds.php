<?php

declare(strict_types=1);

namespace Geokit;

final class Bounds
{
    private $southWest;
    private $northEast;

    private static $southWestKeys = [
        'southwest',
        'south_west',
        'southWest'
    ];

    private static $northEastKeys = [
        'northeast',
        'north_east',
        'northEast'
    ];

    public function __construct(LatLng $southWest, LatLng $northEast)
    {
        $this->southWest = $southWest;
        $this->northEast = $northEast;

        if ($this->southWest->getLatitude() > $this->northEast->getLatitude()) {
            throw new \LogicException('Bounds south-west coordinate cannot be north of the north-east coordinate');
        }
    }

    public function getSouthWest(): LatLng
    {
        return $this->southWest;
    }

    public function getNorthEast(): LatLng
    {
        return $this->northEast;
    }

    public function getCenter(): LatLng
    {
        if ($this->crossesAntimeridian()) {
            $span = $this->lngSpan($this->southWest->getLongitude(), $this->northEast->getLongitude());
            $lng = $this->southWest->getLongitude() + $span / 2;
        } else {
            $lng = ($this->southWest->getLongitude() + $this->northEast->getLongitude()) / 2;
        }

        return new LatLng(
            ($this->southWest->getLatitude() + $this->northEast->getLatitude()) / 2,
            $lng
        );
    }

    public function getSpan(): LatLng
    {
        return new LatLng(
            $this->northEast->getLatitude() - $this->southWest->getLatitude(),
            $this->lngSpan($this->southWest->getLongitude(), $this->northEast->getLongitude())
        );
    }

    public function crossesAntimeridian(): bool
    {
        return $this->southWest->getLongitude() > $this->northEast->getLongitude();
    }

    public function contains(LatLng $latLng): bool
    {
        $lat = $latLng->getLatitude();

        // check latitude
        if (
            $this->southWest->getLatitude() > $lat ||
            $lat > $this->northEast->getLatitude()
        ) {
            return false;
        }

        // check longitude
        return $this->containsLng($latLng->getLongitude());
    }

    public function extend(LatLng $latLng): self
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

    public function union(Bounds $bounds): self
    {
        $newBounds = $this->extend($bounds->getSouthWest());

        return $newBounds->extend($bounds->getNorthEast());
    }

    /**
     * Returns whether or not the given line of longitude is inside the bounds.
     */
    private function containsLng(float $lng): bool
    {
        if ($this->crossesAntimeridian()) {
            return $lng <= $this->northEast->getLongitude() ||
                $lng >= $this->southWest->getLongitude();
        }

        return $this->southWest->getLongitude() <= $lng &&
            $lng <= $this->northEast->getLongitude();
    }

    /**
     * Gets the longitudinal span of the given west and east coordinates.
     */
    private function lngSpan(float $west, float $east): float
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
     * north-east at index 1, eg. [[-45.0, 180.0], [45.0, -180.0]].
     *
     * If $input is an Bounds object, it is just passed through.
     *
     * @param mixed $input
     */
    public static function normalize($input): self
    {
        if ($input instanceof self) {
            return $input;
        }

        $southWest = null;
        $northEast = null;

        if (is_string($input) && preg_match('/(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)[, ] ?(\-?\d+\.?\d*)$/', $input, $match)) {
            $southWest = ['lat' => $match[1], 'lng' => $match[2]];
            $northEast = ['lat' => $match[3], 'lng' => $match[4]];
        } elseif (is_array($input) || $input instanceof \ArrayAccess) {
            if (Utils::isNumericInputArray($input)) {
                [$southWest, $northEast] = $input;
            } else {
                $southWest = Utils::extractFromInput($input, self::$southWestKeys);
                $northEast = Utils::extractFromInput($input, self::$northEastKeys);
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
}
