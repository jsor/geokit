Geokit
======

Geokit is a PHP toolkit to solve geo-related tasks like:

* Distance calculations.
* Heading, midpoint and endpoint calculations.
* Rectangular bounding box calculations.

[![Build Status](https://travis-ci.org/jsor/geokit.svg?branch=master)](http://travis-ci.org/jsor/geokit?branch=master)
[![Coverage Status](https://img.shields.io/coveralls/jsor/geokit.svg?style=flat)](https://coveralls.io/r/jsor/geokit?branch=master)

* [Installation](#installation)
* [Reference](#reference)
    * [Math](#math)
        * [Distance calculations](#distance-calculations)
        * [Transformations](#transformations)
        * [Other calculations](#other-calculations)
    * [Distance](#distance)
    * [Position](#position)
    * [BoundingBox](#boundingbox)
    * [Polygon](#polygon)
* [License](#license)
* [Credits](#credits)

Installation
------------

Install the latest version with [Composer](https://getcomposer.org).

```bash
composer require geokit/geokit
```

Check the [Packagist page](https://packagist.org/packages/geokit/geokit) for all
available versions.

Reference
---------

### Math

A Math instance can be used to perform geographic calculations on Position and 
BoundingBox instances.

The [World Geodetic System 1984](http://en.wikipedia.org/wiki/World_Geodetic_System) 
(WGS84) is exclusively used as the coordinate reference system.

```php
$math = new Geokit\Math();
```

#### Distance calculations

A math instance provides two methods to calculate the distance between 2 points
on the Earth's surface:

* `distanceHaversine(Geokit\Position $from, Geokit\Position $to)`: Calculates the
  approximate sea level great circle (Earth) distance between two points using
  the Haversine formula.
* `distanceVincenty(Geokit\Position $from, Geokit\Position $to)`: Calculates the
  geodetic distance between two points using the Vincenty inverse formula for
  ellipsoids.

```php
$distance1 = $math->distanceHaversine($from, $to);
$distance2 = $math->distanceVincenty($from, $to);
```

Both methods return a [Distance](#distance) instance.

#### Transformations

The `circle()` method calculates a closed circle Polygon given a center, radius
and steps for precision.

```php
$circlePolygon = $math->circle(
    new Geokit\Position(8.50207515, 49.50042565), 
    Geokit\Distance::fromString('5km'),
    32
);
```

#### Other calculations

Other useful methods are:

* `heading(Geokit\Position $from, Geokit\Position $to)`: Calculates the
  (initial) heading from the first point to the second point in degrees.
* `midpoint(Geokit\Position $from, Geokit\Position $to)`: Calculates an
  intermediate point on the geodesic between the two given points.
* `endpoint(Geokit\Position $start, float $heading, Geokit\Distance $distance)`:
  Calculates the destination point along a geodesic, given an initial heading
  and distance, from the given start point.

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
$yards = $distance->yards();
$feet = $distance->feet();
$inches = $distance->inches();
$nauticalMiles = $distance->nautical();
```

A Distance can also be created from a string with an optional unit.

```php
$distance = Geokit\Distance::fromString('1000'); // Defaults to meters
$distance = Geokit\Distance::fromString('1000m');
$distance = Geokit\Distance::fromString('1km');
$distance = Geokit\Distance::fromString('100 miles');
$distance = Geokit\Distance::fromString('100 yards');
$distance = Geokit\Distance::fromString('1 foot');
$distance = Geokit\Distance::fromString('1 inch');
$distance = Geokit\Distance::fromString('234nm');
```

### Position

A `Position` is a fundamental construct representing a geographical position in
`x` (or `longitude`) and `y` (or `latitude`) coordinates.

Note, that `x`/`y` coordinates are kept as is, while `longitude`/`latitude` are
normalized.

* Longitudes range between -180 and 180 degrees, inclusive. Longitudes above 180
  or below -180 are normalized. For example, 480, 840 and 1200 will all be
  normalized to 120 degrees.
* Latitudes range between -90 and 90 degrees, inclusive. Latitudes above 90 or
  below -90 are normalized. For example, 100 will be normalized to 80 degrees.

```php
$position = new Geokit\Position(181, 91);

$x = $position->x(); // Returns 181.0
$y = $position->y(); // Returns 91.0
$longitude = $position->longitude(); // Returns -179.0, normalized
$latitude = $position->latitude(); // Returns 89.0, normalized
```

### BoundingBox

A BoundingBox instance represents a rectangle in geographical coordinates,
including one that crosses the 180 degrees longitudinal meridian.

It is constructed from its left-bottom (south-west) and right-top (north-east)
corner points.

```php
$southWest = new Geokit\Position(2, 1);
$northEast = new Geokit\Position(2, 1);

$boundingBox = new Geokit\BoundingBox($southWest, $northEast);

$southWestPosition = $boundingBox->southWest();
$northEastPosition = $boundingBox->northEast();

$center = $boundingBox->center();

$span = $boundingBox->span();

$boolean = $boundingBox->contains($position);

$newBoundingBox = $boundingBox->extend($position);
$newBoundingBox = $boundingBox->union($otherBoundingBox);
```

With the `expand()` and `shrink()` methods, you can expand or shrink a
BoundingBox instance by a distance.

```php
$expandedBoundingBox = $boundingBox->expand(
    Geokit\Distance::fromString('10km')
);

$shrinkedBoundingBox = $boundingBox->shrink(
    Geokit\Distance::fromString('10km')
);
```

The `toPolygon()` method converts the BoundingBox to an equivalent Polygon
instance.

```php
$polygon = $boundingBox->toPolygon();
```

### Polygon

A Polygon instance represents a two-dimensional shape of connected line segments
and may either be closed (the first and last point are the same) or open.

```php
$polygon = new Geokit\Polygon([
    new Geokit\Position(0, 0),
    new Geokit\Position(1, 0),
    new Geokit\Position(1, 1)
]);

$closedPolygon = $polygon->close();

/** @var Geokit\Position $position */
foreach ($polygon as $position) {
}

$polygon->contains(Geokit\Position(0.5, 0.5)); // true

/** @var Geokit\BoundingBox $boundingBox */
$boundingBox = $polygon->toBoundingBox();
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
