<?php

declare(strict_types=1);

namespace Geokit;

use Geokit\Exception\MissingCoordinateException;
use JsonSerializable;
use function array_key_exists;
use function asin;
use function cos;
use function deg2rad;
use function max;
use function min;
use function rad2deg;
use function sin;

final class BoundingBox implements JsonSerializable
{
    /** @var Position */
    private $southWest;

    /** @var Position */
    private $northEast;

    public function __construct(Position $southWest, Position $northEast)
    {
        $this->southWest = $southWest;
        $this->northEast = $northEast;

        if ($this->southWest->latitude() > $this->northEast->latitude()) {
            throw new Exception\LogicException(
                'Bounding Box south-west coordinate cannot be north of the north-east coordinate'
            );
        }
    }

    /**
     * @param iterable<float> $iterable
     */
    public static function fromCoordinates(iterable $iterable) : BoundingBox
    {
        $array = [];

        foreach ($iterable as $coordinate) {
            $array[] = $coordinate;

            if (isset($array[3])) {
                break;
            }
        }

        if (!array_key_exists(0, $array)) {
            throw MissingCoordinateException::create('west', 0);
        }

        if (!array_key_exists(1, $array)) {
            throw MissingCoordinateException::create('south', 1);
        }

        if (!array_key_exists(2, $array)) {
            throw MissingCoordinateException::create('east', 0);
        }

        if (!array_key_exists(3, $array)) {
            throw MissingCoordinateException::create('north', 1);
        }

        return new self(
            new Position($array[0], $array[1]),
            new Position($array[2], $array[3])
        );
    }

    /**
     * @return iterable<float>
     */
    public function toCoordinates() : iterable
    {
        return [
            $this->southWest->x(),
            $this->southWest->y(),
            $this->northEast->x(),
            $this->northEast->y(),
        ];
    }

    /**
     * @return array<float>
     */
    public function jsonSerialize() : array
    {
        return [
            $this->southWest->x(),
            $this->southWest->y(),
            $this->northEast->x(),
            $this->northEast->y(),
        ];
    }

    public function southWest() : Position
    {
        return $this->southWest;
    }

    public function northEast() : Position
    {
        return $this->northEast;
    }

    public function center() : Position
    {
        if ($this->crossesAntimeridian()) {
            $span = $this->lngSpan(
                $this->southWest->longitude(),
                $this->northEast->longitude()
            );
            $lng  = $this->southWest->longitude() + $span / 2;
        } else {
            $lng = ($this->southWest->longitude() + $this->northEast->longitude()) / 2;
        }

        return new Position(
            $lng,
            ($this->southWest->latitude() + $this->northEast->latitude()) / 2
        );
    }

    public function span() : Position
    {
        return new Position(
            $this->lngSpan($this->southWest->longitude(), $this->northEast->longitude()),
            $this->northEast->latitude() - $this->southWest->latitude()
        );
    }

    public function crossesAntimeridian() : bool
    {
        return $this->southWest->longitude() > $this->northEast->longitude();
    }

    public function contains(Position $position) : bool
    {
        $lat = $position->latitude();

        // check latitude
        if ($this->southWest->latitude() > $lat ||
            $lat > $this->northEast->latitude()
        ) {
            return false;
        }

        // check longitude
        return $this->containsLng($position->longitude());
    }

    public function extend(Position $position) : self
    {
        $newSouth = min($this->southWest->latitude(), $position->latitude());
        $newNorth = max($this->northEast->latitude(), $position->latitude());

        $newWest = $this->southWest->longitude();
        $newEast = $this->northEast->longitude();

        if (!$this->containsLng($position->longitude())) {
            // try extending east and try extending west, and use the one that
            // has the smaller longitudinal span
            $extendEastLngSpan = $this->lngSpan($newWest, $position->longitude());
            $extendWestLngSpan = $this->lngSpan($position->longitude(), $newEast);

            if ($extendEastLngSpan <= $extendWestLngSpan) {
                $newEast = $position->longitude();
            } else {
                $newWest = $position->longitude();
            }
        }

        return new self(new Position($newWest, $newSouth), new Position($newEast, $newNorth));
    }

    public function union(BoundingBox $bbox) : self
    {
        $newBbox = $this->extend($bbox->southWest());

        return $newBbox->extend($bbox->northEast());
    }

    public function expand(Distance $distance) : BoundingBox
    {
        return self::transformBoundingBox($this, $distance->meters());
    }

    public function shrink(Distance $distance) : BoundingBox
    {
        return self::transformBoundingBox($this, -$distance->meters());
    }

    public function toPolygon() : Polygon
    {
        return new Polygon([
            new Position($this->southWest->longitude(), $this->southWest->latitude()),
            new Position($this->northEast->longitude(), $this->southWest->latitude()),
            new Position($this->northEast->longitude(), $this->northEast->latitude()),
            new Position($this->southWest->longitude(), $this->northEast->latitude()),
            new Position($this->southWest->longitude(), $this->southWest->latitude()),
        ]);
    }

    /**
     * @see http://janmatuschek.de/LatitudeLongitudeBoundingCoordinates
     */
    private static function transformBoundingBox(BoundingBox $bbox, float $distanceInMeters) : BoundingBox
    {
        $latSW = deg2rad($bbox->southWest()->latitude());
        $lngSW = deg2rad($bbox->southWest()->longitude());

        $latNE = deg2rad($bbox->northEast()->latitude());
        $lngNE = deg2rad($bbox->northEast()->longitude());

        $angularDistance = $distanceInMeters / Earth::RADIUS;

        $minLat = $latSW - $angularDistance;
        $maxLat = $latNE + $angularDistance;

        $deltaLonSW = asin(sin($angularDistance) / cos($latSW));
        $deltaLonNE = asin(sin($angularDistance) / cos($latNE));

        $minLon = $lngSW - $deltaLonSW;
        $maxLon = $lngNE + $deltaLonNE;

        $positionSW = new Position(rad2deg($minLon), rad2deg($minLat));
        $positionNE = new Position(rad2deg($maxLon), rad2deg($maxLat));

        // Check if we're shrinking too much
        if ($positionSW->latitude() > $positionNE->latitude()) {
            $center = $bbox->center();

            return new BoundingBox($center, $center);
        }

        return new BoundingBox($positionSW, $positionNE);
    }

    private function containsLng(float $lng) : bool
    {
        if ($this->crossesAntimeridian()) {
            return $lng <= $this->northEast->longitude() ||
                $lng >= $this->southWest->longitude();
        }

        return $this->southWest->longitude() <= $lng &&
            $lng <= $this->northEast->longitude();
    }

    private function lngSpan(float $west, float $east) : float
    {
        return $west > $east ? ($east + 360 - $west) : ($east - $west);
    }
}
