<?php

declare(strict_types=1);

namespace Geokit;

final class Bounds
{
    private $southWest;
    private $northEast;

    public function __construct(LatLng $southWest, LatLng $northEast)
    {
        $this->southWest = $southWest;
        $this->northEast = $northEast;

        if ($this->southWest->getLatitude() > $this->northEast->getLatitude()) {
            throw new \LogicException(
                'Bounds south-west coordinate cannot be north of the north-east coordinate'
            );
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
            $span = $this->lngSpan(
                $this->southWest->getLongitude(),
                $this->northEast->getLongitude()
            );
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

    private function containsLng(float $lng): bool
    {
        if ($this->crossesAntimeridian()) {
            return $lng <= $this->northEast->getLongitude() ||
                $lng >= $this->southWest->getLongitude();
        }

        return $this->southWest->getLongitude() <= $lng &&
            $lng <= $this->northEast->getLongitude();
    }

    private function lngSpan(float $west, float $east): float
    {
        return ($west > $east) ? ($east + 360 - $west) : ($east - $west);
    }
}
