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

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class HostipGeocoder extends AbstractGeocoder
{
    /**
     * The Google API Uri.
     *
     * @var string
     */
    private $apiUri = 'http://api.hostip.info/get_xml.php';

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
     * {@inheritDoc}
     */
    public function geocodeIp($ip)
    {
        $params = array(
            'ip'       => $ip,
            'position' => 'true'
        );

        $url = $this->getApiUri().'?'.http_build_query($params, null, '&');

        $response = $this->getBrowser()->get($url);
        $statusCode = (integer) $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 300) {
            return new Response($statusCode, $this);
        }

        $xml = new \SimpleXMLElement($response->getContent());

        $coords = $xml->xpath('//gml:coordinates');

        if (count($coords) == 0) {
            return new Response(404, $this);
        }

        list($longitude, $latitude) = explode(',', (string) $coords[0]);

        $locality    = $xml->xpath('//gml:featureMember//gml:name');
        $countryName = $xml->xpath('//gml:featureMember//countryName');
        $countryCode = $xml->xpath('//gml:featureMember//countryAbbrev');

        $location = $this->getLocationFactory()->createLocation(
            new LatLng($latitude, $longitude),
            LocationInterface::ACCURACY_UNKNOWN,
            null,
            null,
            null,
            null,
            null,
            null,
            (string) $locality[0],
            null,
            (string) $countryName[0],
            (string) $countryCode[0]
        );

        return new Response(200, $this, $location);
    }
}
