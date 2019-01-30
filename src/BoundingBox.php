<?php

declare(strict_types=1);

namespace Geokit;

final class BoundingBox
{
    private $southWest;
    private $northEast;

    public function __construct(LatLng $southWest, LatLng $northEast)
    {
        $this->southWest = $southWest;
        $this->northEast = $northEast;

        if ($this->southWest->latitude() > $this->northEast->latitude()) {
            throw new Exception\LogicException(
                'Bounding Box south-west coordinate cannot be north of the north-east coordinate'
            );
        }
    }

    public function southWest(): LatLng
    {
        return $this->southWest;
    }

    public function northEast(): LatLng
    {
        return $this->northEast;
    }

    public function center(): LatLng
    {
        if ($this->crossesAntimeridian()) {
            $span = $this->lngSpan(
                $this->southWest->longitude(),
                $this->northEast->longitude()
            );
            $lng = $this->southWest->longitude() + $span / 2;
        } else {
            $lng = ($this->southWest->longitude() + $this->northEast->longitude()) / 2;
        }

        return new LatLng(
            ($this->southWest->latitude() + $this->northEast->latitude()) / 2,
            $lng
        );
    }

    public function span(): LatLng
    {
        return new LatLng(
            $this->northEast->latitude() - $this->southWest->latitude(),
            $this->lngSpan($this->southWest->longitude(), $this->northEast->longitude())
        );
    }

    public function crossesAntimeridian(): bool
    {
        return $this->southWest->longitude() > $this->northEast->longitude();
    }

    public function contains(LatLng $latLng): bool
    {
        $lat = $latLng->latitude();

        // check latitude
        if (
            $this->southWest->latitude() > $lat ||
            $lat > $this->northEast->latitude()
        ) {
            return false;
        }

        // check longitude
        return $this->containsLng($latLng->longitude());
    }

    public function extend(LatLng $latLng): self
    {
        $newSouth = \min($this->southWest->latitude(), $latLng->latitude());
        $newNorth = \max($this->northEast->latitude(), $latLng->latitude());

        $newWest = $this->southWest->longitude();
        $newEast = $this->northEast->longitude();

        if (!$this->containsLng($latLng->longitude())) {
            // try extending east and try extending west, and use the one that
            // has the smaller longitudinal span
            $extendEastLngSpan = $this->lngSpan($newWest, $latLng->longitude());
            $extendWestLngSpan = $this->lngSpan($latLng->longitude(), $newEast);

            if ($extendEastLngSpan <= $extendWestLngSpan) {
                $newEast = $latLng->longitude();
            } else {
                $newWest = $latLng->longitude();
            }
        }

        return new self(new LatLng($newSouth, $newWest), new LatLng($newNorth, $newEast));
    }

    public function union(BoundingBox $bbox): self
    {
        $newBbox = $this->extend($bbox->southWest());

        return $newBbox->extend($bbox->northEast());
    }

    public function expand(Distance $distance): BoundingBox
    {
        return self::transformBoundingBox($this, $distance->meters());
    }

    public function shrink(Distance $distance): BoundingBox
    {
        return self::transformBoundingBox($this, -$distance->meters());
    }

    public function toPolygon(): Polygon
    {
        return new Polygon([
            new LatLng($this->southWest->latitude(), $this->southWest->longitude()),
            new LatLng($this->southWest->latitude(), $this->northEast->longitude()),
            new LatLng($this->northEast->latitude(), $this->northEast->longitude()),
            new LatLng($this->northEast->latitude(), $this->southWest->longitude()),
            new LatLng($this->southWest->latitude(), $this->southWest->longitude()),
        ]);
    }

    private static function transformBoundingBox(BoundingBox $bbox, float $distanceInMeters): BoundingBox
    {
        $latSW = $bbox->southWest()->latitude();
        $lngSW = $bbox->southWest()->longitude();
        $latNE = $bbox->northEast()->latitude();
        $lngNE = $bbox->northEast()->longitude();

        $latlngSW = new LatLng(
            self::latDistance($latSW, $distanceInMeters),
            self::lngDistance($latSW, $lngSW, $distanceInMeters)
        );

        $latlngNE = new LatLng(
            self::latDistance($latNE, -$distanceInMeters),
            self::lngDistance($latNE, $lngNE, -$distanceInMeters)
        );

        // Check if we're shrinking too much
        if ($latlngSW->latitude() > $latlngNE->latitude()) {
            $center = $bbox->center();

            return new BoundingBox($center, $center);
        }

        return new BoundingBox($latlngSW, $latlngNE);
    }

    private static function lngDistance(float $lat1, float $lng1, float $distanceInMeters): float
    {
        $radius = Earth::RADIUS;

        $lat1 = \deg2rad($lat1);
        $lng1 = \deg2rad($lng1);

        $lng2 = ($radius * $lng1 * \cos($lat1) - $distanceInMeters) / ($radius * \cos($lat1));

        return Utils::normalizeLng(\rad2deg($lng2));
    }

    private static function latDistance(float $lat1, float $distanceInMeters): float
    {
        $radius = Earth::RADIUS;

        $lat1 = \deg2rad($lat1);
        $lat2 = ($radius * $lat1 - $distanceInMeters) / $radius;

        return Utils::normalizeLat(\rad2deg($lat2));
    }

    private function containsLng(float $lng): bool
    {
        if ($this->crossesAntimeridian()) {
            return $lng <= $this->northEast->longitude() ||
                $lng >= $this->southWest->longitude();
        }

        return $this->southWest->longitude() <= $lng &&
            $lng <= $this->northEast->longitude();
    }

    private function lngSpan(float $west, float $east): float
    {
        return ($west > $east) ? ($east + 360 - $west) : ($east - $west);
    }
}
