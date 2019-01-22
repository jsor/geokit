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

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function __toString(): string
    {
        return \sprintf('%F,%F', $this->getLatitude(), $this->getLongitude());
    }
}
