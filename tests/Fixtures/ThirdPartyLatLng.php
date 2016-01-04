<?php

namespace Geokit\Fixtures;

class ThirdPartyLatLng
{
    private $lat;
    private $lng;

    public function __construct($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getLatitude()
    {
        return $this->lat;
    }

    public function getLongitude()
    {
        return $this->lng;
    }
}
