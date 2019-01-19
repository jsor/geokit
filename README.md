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

* `distanceHaversine(LatLng $from, LatLng $to)`: Calculates the approximate sea
  level great circle (Earth) distance between two points using the Haversine
  formula.
* `distanceVincenty(LatLng $from, LatLng $to)`: Calculates the geodetic distance
  between two points using the Vincenty inverse formula for ellipsoids.

```php
$distance1 = $math->distanceHaversine($from, $to);
$distance2 = $math->distanceVincenty($from, $to);
```

Both methods return a [Distance](#distance) instance.

#### Transformations

With the `expandBounds` and `shrinkBounds` methods, you can expand or shrink a
given Bounds instance by a distance.

```php
$expandedBounds1 = $math->expandBounds(new LatLng(49.50042565, 8.50207515), Distance::fromString('10km'));

$shrinkedBounds = $math->shrinkBounds($expandedBounds2, Distance::fromString('10km'));
```

#### Other calculations

Other useful methods are:

* `heading(LatLng $from, LatLng $to)`: Calculates the (initial) heading from the
  first point to the second point in degrees.
* `midpoint(LatLng $from, LatLng $to)`: Calculates an intermediate point on the
  geodesic between the two given points.
* `endpoint(LatLng $start, float $heading, Distance $distance)`: Calculates the
  destination point along a geodesic, given an initial heading and distance, 
  from the given start point.

### Distance

A Distance instance allows for a convenient representation of a distance unit of
measure.

```php
$distance = new Geokit\Distance(1000); // Defaults to meters
// or
$distance = new Geokit\Distance(1, Geokit\Distance::UNIT_KILOMETERS);

$meters = $distance->meters();
$kilometers = $distance->kilometers();
$miles = $distance->miles();
$feet = $distance->feet();
$nauticalMiles = $distance->nautical();
```

A Distance can also be created from a string with an optional unit.

```php
$distance = Geokit\Distance::fromString('1000'); // Defaults to meters
$distance = Geokit\Distance::fromString('1000m');
$distance = Geokit\Distance::fromString('1km');
$distance = Geokit\Distance::fromString('100 miles');
$distance = Geokit\Distance::fromString('1 foot');
$distance = Geokit\Distance::fromString('234nm');
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
$longitude = $latLng->getLongitude();
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
$northEastLatLng = $bounds->getNorthEast();

$centerLatLng = $bounds->getCenter();

$spanLatLng = $bounds->getSpan();

$boolean = $bounds->contains($latLng);

$newBounds = $bounds->extend($latLng);
$newBounds = $bounds->union($otherBounds);
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

$closedPolygon = $polygon->close();

/** @var Geokit\LatLng $latLng */
foreach ($polygon as $latLng) {
}

$polygon->contains(LatLng(0.5, 0.5)); // true

/** @var Geokit\Bounds $bounds */
$bounds = $polygon->toBounds();
```

License
-------

Copyright (c) 2011-2019 Jan Sorgalla. 
Released under the [MIT License](LICENSE).

Credits
-------

Geokit has been inspired and/or contains ported code from the following
libraries:

* [OpenLayers](https://github.com/openlayers/openlayers)
* [GeoPy](https://github.com/geopy/geopy)
* [scalaz-geo](https://github.com/scalaz/scalaz-geo)
* [geojs](http://code.google.com/p/geojs)
