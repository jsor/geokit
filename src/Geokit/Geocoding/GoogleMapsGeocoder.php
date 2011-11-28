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

use Geokit\Geocoding\AbstractGeocoder;
use Geokit\Geocoding\LocationInterface;
use Geokit\Geocoding\Response;
use Geokit\LatLng;
use Geokit\Bounds;
use Geokit\Util;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class GoogleMapsGeocoder extends AbstractGeocoder
{
    /**
     * The Google API Uri.
     *
     * @var string
     */
    private $apiUri = 'http://maps.google.com/maps/api/geocode/json';

    /**
     * Request params.
     *
     * @var array
     */
    private $requestParams = array();

    /**
     * @param string $apiUri
     * @return GoogleMapsGeocoder
     */
    public function setApiUri($apiUri)
    {
        $this->apiUri = $apiUri;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiUri()
    {
        return $this->apiUri;
    }

    /**
     * @param array $requestParams
     * @return GoogleMapsGeocoder
     */
    public function setRequestParams(array $requestParams)
    {
        $this->requestParams = $requestParams;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * {@inheritDoc}
     */
    public function geocodeAddress($address)
    {
        $params = array('address' => $address);
        return $this->_doRequest($params);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseGeocodeLatLng($latLng)
    {
        $latLng = Util::normalizeLatLng($latLng);

        $params = array('latlng' => sprintf('%F,%F', $latLng->getLatitude(), $latLng->getLongitude()));
        return $this->_doRequest($params);
    }

    /**
     * Request googles geocoding service.
     *
     * @param array $params
     * @return Response
     */
    protected function _doRequest(array $params)
    {
        $default = array('sensor'   => 'false',
                         'language' => 'en');

        $params += $this->getRequestParams() + $default;

        $url = $this->getApiUri().'?'.http_build_query($params, null, '&');

        $response = $this->getBrowser()->get($url);
        $statusCode = (integer) $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            return new Response($statusCode, $this);
        }

        $body = json_decode($response->getContent(), true);

        if ('OK' !== $body['status']) {
            switch ($body['status']) {
                case 'ZERO_RESULTS':
                    $code = 404; // Not Found
                    break;
                case 'OVER_QUERY_LIMIT':
                case 'REQUEST_DENIED':
                    $code = 403; // Forbidden
                    break;
                case 'INVALID_REQUEST':
                default:
                    $code = 400; // Bad Request
                    break;
            }

            return new Response($code, $this);
        }

        $location = null;
        $additionalLocations = array();

        foreach ($body['results'] as $result) {
            $currentLocation = $this->createLocationFromResult($result);

            if (null == $location) {
                $location = $currentLocation;
            } else {
                $additionalLocations[] = $currentLocation;
            }
        }

        return new Response(200, $this, $location, $additionalLocations);
    }

    /**
     * Creates a location from a result.
     *
     * @param array $result
     * @return \Geokit\Geocoding\LocationInterface
     */
    protected function createLocationFromResult(array $result)
    {
        $latLng = new LatLng(
            $result['geometry']['location']['lat'],
            $result['geometry']['location']['lng']
        );

        $bounds = null;
        if (isset($result['geometry']['bounds'])) {
            $bounds = new Bounds(
                new LatLng(
                    $result['geometry']['bounds']['southwest']['lat'],
                    $result['geometry']['bounds']['southwest']['lng']
                ),
                new LatLng(
                    $result['geometry']['bounds']['northeast']['lat'],
                    $result['geometry']['bounds']['northeast']['lng']
                )
            );
        }

        $viewport = null;
        if (isset($result['geometry']['viewport'])) {
            $viewport = new Bounds(
                new LatLng(
                    $result['geometry']['viewport']['southwest']['lat'],
                    $result['geometry']['viewport']['southwest']['lng']
                ),
                new LatLng(
                    $result['geometry']['viewport']['northeast']['lat'],
                    $result['geometry']['viewport']['northeast']['lng']
                )
            );
        }

        switch ($result['geometry']['location_type']) {
            case 'ROOFTOP':
                $accuracy = LocationInterface::ACCURACY_PRECISE;
                break;
            case 'RANGE_INTERPOLATED':
                $accuracy = LocationInterface::ACCURACY_INTERPOLATED;
                break;
            case 'GEOMETRIC_CENTER':
                $accuracy = LocationInterface::ACCURACY_CENTER;
                break;
            case 'APPROXIMATE':
            default:
                $accuracy = LocationInterface::ACCURACY_APPROXIMATE;
                break;
        }

        $formattedAddress = $result['formatted_address'];

        $streetNumber = null;
        $streetName   = null;
        $postalCode   = null;
        $locality     = null;
        $region       = null;
        $countryName  = null;
        $countryCode  = null;

        foreach ($result['address_components'] as $component) {
            if (in_array('street_number', $component['types'])) {
                $streetNumber = $component['long_name'];
            }

            if (in_array('route', $component['types'])) {
                $streetName = $component['long_name'];
            }

            if (in_array('postal_code', $component['types'])) {
                $postalCode = $component['long_name'];
            }

            if (in_array('locality', $component['types'])) {
                $locality = $component['long_name'];
            }

            if (in_array('administrative_area_level_1', $component['types'])) {
                $region = $component['long_name'];
            }

            if (in_array('country', $component['types'])) {
                $countryName = $component['long_name'];
                $countryCode = $component['short_name'];
            }
        }

        return $this->getLocationFactory()->createLocation(
            $latLng,
            $accuracy,
            $bounds,
            $viewport,
            $formattedAddress,
            $streetNumber,
            $streetName,
            $postalCode,
            $locality,
            $region,
            $countryName,
            $countryCode
        );
    }
}
