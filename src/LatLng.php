<?php

declare(strict_types=1);

namespace Geokit;

final class LatLng
{
    private $latitude;
    private $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = Utils::normalizeLat($latitude);
        $this->longitude = Utils::normalizeLng($longitude);
    }

    public function longitude(): float
    {
        return $this->longitude;
    }

    public function latitude(): float
    {
        return $this->latitude;
    }
}
