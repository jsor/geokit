Geokit
======

Geokit is a PHP toolkit to solve geo-related tasks like:

* Distance calculations.
* Heading, midpoint and endpoint calculations.
* Rectangular bounds calculations.

[![Build Status](https://travis-ci.org/jsor/geokit.svg?branch=master)](http://travis-ci.org/jsor/geokit?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/jsor/geokit.svg?style=flat)](https://coveralls.io/r/jsor/geokit?branch=master)

* [Installation](#installation)
* [Reference](#reference)
    * [Math](#math)
        * [Distance calculations](#distance-calculations)
        * [Transformations](#transformations)
        * [Other calculations](#other-calculations)
    * [Distance](#distance)
    * [LatLng](#latlng)
    * [Bounds](#bounds)
    * [Polygon](#polygon)
* [License](#license)
* [Credits](#credits)

Installation
------------

Install the latest version with [Composer](http://getcomposer.org).

```bash
composer require geokit/geokit
```

Check the [Packagist page](https://packagist.org/packages/geokit/geokit) for all
available versions.

Reference
---------

### Math

A Math instance can be used to perform geographic calculations on LatLng and
Bounds instances. Since such calculations depend on an
[Earth Ellipsoid](http://en.wikipedia.org/wiki/Earth_ellipsoid), you can pass an
instance of `Geokit\Ellipsoid` to its constructor. If no Ellipsoid instance is
provided, it uses the default
[WGS 86 Ellipsoid](http://en.wikipedia.org/wiki/World_Geodetic_System).

```php
$math = new Geokit\Math();
$mathAiry = new Geokit\Math(Geokit\Ellipsoid::airy1830());
```

#### Distance calculations

A math instance provides two methods to calculate the distance between 2 points
on the Earth's surface:

* `distanceHaversine($from, $to)`: Calculates the approximate sea level great
   circle (Earth) distance between two points using the Haversine formula.
* `distanceVincenty($from, $to)`: Calculates the geodetic distance between two
   points using the Vincenty inverse formula for ellipsoids.

```php
$distance1 = $math->distanceHaversine($from, $to);
$distance2 = $math->distanceVincenty($from, $to);
```

Both methods return a [Distance](#distance) instance.

#### Transformations

With the `expand` and `shrink` methods, you can expand/shrink a given Bounds or
LatLng instance by a distance.

```php
$expandedBounds1 = $math->expand(['lat' => 49.50042565 'lng' => 8.50207515], '10km');
$expandedBounds2 = $math->expand(Geokit\Bounds::normalize('-45 179 45 -179'), '10km');

$shrinkedBounds = $math->shrink($expandedBounds2, '10km');
```

#### Other calculations

Other useful methods are:

* `heading($from, $to)`: Calculates the (initial) heading from the first point
   to the second point in degrees.
* `midpoint($from, $to)`: Calculates an intermediate point on the geodesic
   between the two given points.
* `endpoint($start, $heading, $distance)`: Calculates the destination point
   along a geodesic, given an initial heading and distance, from the given start
   point.

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

### LatLng

A LatLng instance represents a geographical point in latitude/longitude
coordinates.

* Latitude ranges between -90 and 90 degrees, inclusive. Latitudes above 90 or
  below -90 are capped, not wrapped. For example, 100 will be capped to 90
  degrees.
* Longitude ranges between -180 and 180 degrees, inclusive. Longitudes above 180
  or below -180 are wrapped. For example, 480, 840 and 1200 will all be wrapped
  to 120 degrees.

```php
$latLng = new Geokit\LatLng(1, 2);

$latitude = $latLng->getLatitude();
// or
$latitude = $latLng['latitude'];

$longitude = $latLng->getLongitude();
// or
$longitude = $latLng['longitude'];
```

Alternatively, you can create a LatLng instance through its static normalize
method. This method takes anything which looks like a coordinate and generates a
LatLng object from it.

```php
$latLng = Geokit\LatLng::normalize('1 2');
$latLng = Geokit\LatLng::normalize('1, 2');
$latLng = Geokit\LatLng::normalize(array('latitude' => 1, 'longitude' => 2));
$latLng = Geokit\LatLng::normalize(array('lat' => 1, 'lng' => 2));
$latLng = Geokit\LatLng::normalize(array('lat' => 1, 'lon' => 2));
$latLng = Geokit\LatLng::normalize(array(1, 2));
```

### Bounds

A Bounds instance represents a rectangle in geographical coordinates, including
one that crosses the 180 degrees longitudinal meridian.

It is constructed from its left-bottom (south-west) and right-top (north-east)
corner points.

```php
$southWest = new Geokit\LatLng(1, 2);
$northEast = new Geokit\LatLng(1, 2);

$bounds = new Geokit\Bounds($southWest, $northEast);

$southWestLatLng = $bounds->getSouthWest();
// or
$southWestLatLng = $bounds['south_west'];

$northEastLatLng = $bounds->getNorthEast();
// or
$northEastLatLng = $bounds['north_east'];

$centerLatLng = $bounds->getCenter();
// or
$centerLatLng = $bounds['center'];

$spanLatLng = $bounds->getSpan();
// or
$spanLatLng = $bounds['span'];

$boolean = $bounds->contains($latLng);

$newBounds = $bounds->extend($latLng);
$newBounds = $bounds->union($otherBounds);
```

Alternatively, you can create a Bounds instance through its static normalize
method. This method takes anything which looks like bounds and generates a
Bounds object from it.

```php
$bounds = Geokit\Bounds::normalize('1 2 3 4');
$bounds = Geokit\Bounds::normalize('1 2, 3 4');
$bounds = Geokit\Bounds::normalize('1, 2, 3, 4');
$bounds = Geokit\Bounds::normalize(array('south_west' => $southWestLatLng, 'north_east' => $northEastLatLng));
$bounds = Geokit\Bounds::normalize(array('south_west' => array(1, 2), 'north_east' => array(3, 4)));
$bounds = Geokit\Bounds::normalize(array('southwest' => $southWestLatLng, 'northeast' => $northEastLatLng));
$bounds = Geokit\Bounds::normalize(array('southWest' => $southWestLatLng, 'northEast' => $northEastLatLng));
$bounds = Geokit\Bounds::normalize(array($southWestLatLng, $northEastLatLng));
```

### Polygon

A Polygon instance represents a two-dimensional shape of connected line segments
and may either be closed (the first and last point are the same) or open.

```php
$polygon = new Geokit\Polygon([
    new Geokit\LatLng(0, 0),
    new Geokit\LatLng(0, 1),
    new Geokit\LatLng(1, 1)
]);

$latLng1 = $polygon[0];
$latLng2 = $polygon[1];
$latLng3 = $polygon[2];

$closedPolygon = $polygon->close();

$latLng4 = $closedPolygon[3]; // LatLng(0, 0)

/** @var Geokit\LatLng $latLng */
foreach ($polygon as $latLng) {
}

$polygon->contains(LatLng(0.5, 0.5)); // true

/** @var Geokit\Bounds $bounds */
$bounds = $polygon->toBounds();
```

License
-------

Copyright (c) 2011-2016 Jan Sorgalla. Released under the [MIT License](https://github.com/jsor/geokit/blob/master/LICENSE).

Credits
-------

Geokit has been inspired and/or contains ported code from the following
libraries:

* [OpenLayers](https://github.com/openlayers/openlayers)
* [GeoPy](https://github.com/geopy/geopy)
* [scalaz-geo](https://github.com/scalaz/scalaz-geo)
* [geojs](http://code.google.com/p/geojs)
