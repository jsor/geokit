<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Geocoding;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class MultiGeocoder implements GeocoderInterface
{
    /**
     * @var array
     */
    private $geocoders = array();

    /**
     * @param array $geocoders
     */
    public function __construct(array $geocoders = array())
    {
        foreach ($geocoders as $geocoder) {
            $this->add($geocoder);
        }
    }

    /**
     * @param \Geokit\Geocoding\GeocoderInterface $geocoder
     * @return MultiGeocoder
     */
    public function add(GeocoderInterface $geocoder)
    {
        $this->geocoders[] = $geocoder;

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->geocoders;
    }

    /**
     * {@inheritDoc}
     */
    public function geocodeAddress($address)
    {
        foreach ($this->geocoders as $geocoder) {
            $response = $geocoder->geocodeAddress($address);

            if ($response->isValid()) {
                return $response;
            }
        }

        return new Response(404, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseGeocodeLatLng($latLng)
    {
        foreach ($this->geocoders as $geocoder) {
            $response = $geocoder->reverseGeocodeLatLng($latLng);

            if ($response->isValid()) {
                return $response;
            }
        }

        return new Response(404, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function geocodeIp($ip)
    {
        foreach ($this->geocoders as $geocoder) {
            $response = $geocoder->geocodeIp($ip);

            if ($response->isValid()) {
                return $response;
            }
        }

        return new Response(404, $this);
    }
}
