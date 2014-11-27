Geokit
======

Geokit is a PHP toolkit to solve geo-related tasks like:

* Distance calculations.
* Heading, midpoint and endpoint calculations.
* Rectangular bounds calculations.

[![Build Status](https://travis-ci.org/jsor/geokit.svg?branch=master)](http://travis-ci.org/jsor/geokit?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/jsor/geokit.svg)](https://coveralls.io/r/jsor/geokit?branch=master)

* [Installation](#installation)
* [Reference](#reference)
  * [LngLat](#lnglat)
  * [Bounds](#bounds)
  * [Distance](#distance)
* [License](#license)
* [Credits](#credits)

Installation
------------

Install [through composer](http://getcomposer.org). Check the
[packagist page](https://packagist.org/packages/geokit/geokit) for all
available versions.

```json
{
    "require": {
        "geokit/geokit": "~1.0.0@dev"
    }
}
```

Reference
---------

### LngLat

A LngLat instance represents a geographical point in longitude/latitude
coordinates.

* Longitude ranges between -180 and 180 degrees, inclusive. Longitudes above 180
  or below -180 are wrapped. For example, 480, 840 and 1200 will all be wrapped
  to 120 degrees.
* Latitude ranges between -90 and 90 degrees, inclusive. Latitudes above 90 or
  below -90 are capped, not wrapped. For example, 100 will be capped to 90
  degrees.

```php
$lngLat = new Geokit\LngLat(1, 2);

$longitude = $lngLat->getLongitude();
// or
$longitude = $lngLat['longitude'];

$latitude = $lngLat->getLatitude();
// or
$latitude = $lngLat['latitude'];
```

Alternatively, you can create a LngLat instance through its static normalize
method. This method takes anything which looks like a coordinate and generates a
LngLat object from it.

```php
$lngLat = Geokit\LngLat::normalize('1 2');
$lngLat = Geokit\LngLat::normalize('1, 2');
$lngLat = Geokit\LngLat::normalize(array('longitude' => 1, 'latitude' => 2));
$lngLat = Geokit\LngLat::normalize(array('lng' => 1, 'lat' => 2));
$lngLat = Geokit\LngLat::normalize(array('lon' => 1, 'lat' => 2));
$lngLat = Geokit\LngLat::normalize(array(1, 2));
```

### Bounds

A Bounds instance represents a rectangle in geographical coordinates, including
one that crosses the 180 degrees longitudinal meridian.

It is constructed from its left/bottom (west-south) and right-top (east-north)
corner points.

```php
$westSouth = new Geokit\LngLat(1, 2);
$eastNorth = new Geokit\LngLat(1, 2);

$bounds = new Geokit\Bounds($westSouth, $eastNorth);

$westSouthLngLat = $bounds->getWestSouth();
// or
$westSouthLngLat = $bounds['west_south'];

$eastNorthLngLat = $bounds->getEastNorth();
// or
$eastNorthLngLat = $bounds['east_north'];

$centerLngLat = $bounds->getCenter();
// or
$centerLngLat = $bounds['center'];

$spanLngLat = $bounds->getSpan();
// or
$spanLngLat = $bounds['span'];

$boolean = $bounds->contains($latLng);

$newBounds = $bounds->extend($latLng);
$newBounds = $bounds->union($otherBounds);
```

Alternatively, you can create a Bounds instance through its static normalize
method. This method takes anything which looks like bounds and generates a
Bounds object from it.

```php
$lngLat = Geokit\Bounds::normalize('1 2 3 4');
$lngLat = Geokit\Bounds::normalize('1 2, 3 4');
$lngLat = Geokit\Bounds::normalize('1, 2, 3, 4');
$lngLat = Geokit\Bounds::normalize(array('west_south' => $westSouthLngLat, 'east_north' => $eastNorthLngLat));
$lngLat = Geokit\Bounds::normalize(array('west_south' => array(1, 2), 'east_north' => array(3, 4)));
$lngLat = Geokit\Bounds::normalize(array('westsouth' => $westSouthLngLat, 'eastnorth' => $eastNorthLngLat));
$lngLat = Geokit\Bounds::normalize(array('westSouth' => $westSouthLngLat, 'eastNorth' => $eastNorthLngLat));
$lngLat = Geokit\Bounds::normalize(array($westSouthLngLat, $eastNorthLngLat));
```

### Distance

A Distance instance allows for a convenient representation of a distance unit of
measure.

```php
$distance = new Geokit\Distance(1000);
// or
$distance = new Geokit\Distance(1, Geokit\Distance::UNIT_KILOMETERS);

$meters = $distance->meters();
$kilometers = $distance->kilometers();
$miles = $distance->miles();
$feet = $distance->feet();
$nauticalMiles = $distance->nautical();
```

Alternatively, you can create a Distance instance through its static normalize
method. This method takes anything which looks like distance and generates a
Distance object from it.

```php
$distance = Geokit\Distance::normalize(1000); // Defaults to meters
$distance = Geokit\Distance::normalize('1000m');
$distance = Geokit\Distance::normalize('1km');
$distance = Geokit\Distance::normalize('100 miles');
$distance = Geokit\Distance::normalize('1 foot');
$distance = Geokit\Distance::normalize('234nm');
```

License
-------

Geokit is released under the [MIT License](https://github.com/jsor/Geokit/blob/master/LICENSE).

Credits
-------

Geokit has been inspired and/or contains ported code from the following
libraries:

* [OpenLayers](https://github.com/openlayers/openlayers)
* [GeoPy](https://github.com/geopy/geopy)
* [scalaz-geo](https://github.com/scalaz/scalaz-geo)
* [geojs](http://code.google.com/p/geojs)
