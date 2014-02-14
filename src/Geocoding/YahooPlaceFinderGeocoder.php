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
use Buzz\Browser;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class YahooPlaceFinderGeocoder extends AbstractGeocoder
{
    /**
     * The application ID.
     *
     * @var string
     */
    private $appId;

    /**
     * The Yahoo PlaceFinder Uri.
     *
     * @var string
     */
    private $apiUri = 'http://where.yahooapis.com/geocode';

    /**
     * Request params.
     *
     * @var array
     */
    private $requestParams = array();

    /**
     * @param string $appId
     * @param \Geokit\Geocoding\LocationFactoryInterface $locationFactory
     * @param \Buzz\Browser $browser
     */
    public function __construct($appId, LocationFactoryInterface $locationFactory = null, Browser $browser = null)
    {
        parent::__construct($locationFactory, $browser);

        $this->setAppId($appId);
    }

    /**
     * @param string $appId
     * @return YahooPlaceFinderGeocoder
     */
    public function setAppId($appId)
    {
        if (empty($appId)) {
            throw new \InvalidArgumentException('The application id is empty');
        }

        $this->appId = $appId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $apiUri
     * @return YahooPlaceFinderGeocoder
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
     * @return YahooPlaceFinderGeocoder
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
        $params = array('q' => $address);
        return $this->_doRequest($params);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseGeocodeLatLng($latLng)
    {
        $latLng = Util::normalizeLatLng($latLng);

        $params = array(
            'q'      => sprintf('%F,%F', $latLng->getLatitude(), $latLng->getLongitude()),
            'gflags' => 'R'
        );

        return $this->_doRequest($params);
    }
    
    /**
     * {@inheritDoc}
     */
    public function geocodeIp($ip)
    {
        $params = array('q' => $ip);
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
        $default = array('appid'  => $this->getAppId(),
                         'locale' => 'en_US',
                         'flags'  => 'J');

        $params += $this->getRequestParams() + $default;

        $params['flags'] = str_replace('P', '', $params['flags']);
        if (false === strpos($params['flags'], 'J')) {
            $params['flags'] .= 'J';
        }
        if (false === strpos($params['flags'], 'X')) {
            $params['flags'] .= 'X'; // Add boundingbox
        }

        $url = $this->getApiUri().'?'.http_build_query($params, null, '&');

        $response = $this->getBrowser()->get($url);
        $statusCode = (integer) $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            return new Response($statusCode, $this);
        }

        $body = json_decode($response->getContent(), true);

        if ($body['ResultSet']['Error'] > 0) {
            return new Response(400, $this);  // Bad Request
        }

        if ($body['ResultSet']['Found'] <= 0) {
            return new Response(404, $this);  // Not Found
        }

        $location = null;
        $additionalLocations = array();

        foreach ($body['ResultSet']['Results'] as $result) {
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
            $result['latitude'],
            $result['longitude']
        );

        $bounds = null;
        if (isset($result['boundingbox'])) {
            $bounds = new Bounds(
                new LatLng(
                    $result['boundingbox']['south'],
                    $result['boundingbox']['west']
                ),
                new LatLng(
                    $result['boundingbox']['north'],
                    $result['boundingbox']['east']
                )
            );
        }

        $viewport = null; // Not supported

        if ($result['quality'] >= 87) {
            $accuracy = LocationInterface::ACCURACY_PRECISE;
        } elseif ($result['quality'] >= 70) {
            $accuracy = LocationInterface::ACCURACY_INTERPOLATED;
        } elseif ($result['quality'] >= 9) {
            $accuracy = LocationInterface::ACCURACY_CENTER;
        } else {
            $accuracy = LocationInterface::ACCURACY_APPROXIMATE;
        }

        $formattedAddress = null; // Not supported

        $streetNumber = (string) $result['house'] != ''       ? $result['house'] :       null;
        $streetName   = (string) $result['street'] != ''      ? $result['street'] :      null;
        $postalCode   = (string) $result['postal'] != ''      ? $result['postal'] :      null;
        $locality     = (string) $result['city'] != ''        ? $result['city'] :        null;
        $region       = (string) $result['state'] != ''       ? $result['state'] :       null;
        $countryName  = (string) $result['country'] != ''     ? $result['country'] :     null;
        $countryCode  = (string) $result['countrycode'] != '' ? $result['countrycode'] : null;

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
