Geokit
======

Geokit is a PHP toolkit to solve geo-related tasks like:

* Distance calculations.
* Heading, midpoint and endpoint calculations.
* Rectangular bounding box calculations.

[![Build Status](https://github.com/jsor/geokit/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/jsor/geokit/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/jsor/geokit/badge.svg?branch=main&service=github)](https://coveralls.io/github/jsor/geokit?branch=main)

* [Installation](#installation)
* [Reference](#reference)
    * [Distance](#distance)
    * [Position](#position)
    * [BoundingBox](#boundingbox)
    * [Polygon](#polygon)
    * [Functions](#functions)
        * [Distance calculations](#distance-calculations)
        * [Transformations](#transformations)
        * [Other calculations](#other-calculations)
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

### Distance

A Distance instance allows for a convenient representation of a distance unit of
measure.

```php
use Geokit\Distance;

$distance = new Distance(1000); // Defaults to meters
// or
$distance = new Distance(1, Distance::UNIT_KILOMETERS);

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
use Geokit\Distance;

$distance = Distance::fromString('1000'); // Defaults to meters
$distance = Distance::fromString('1000m');
$distance = Distance::fromString('1km');
$distance = Distance::fromString('100 miles');
$distance = Distance::fromString('100 yards');
$distance = Distance::fromString('1 foot');
$distance = Distance::fromString('1 inch');
$distance = Distance::fromString('234nm');
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
use Geokit\Position;

$position = new Position(181, 91);

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
use Geokit\BoundingBox;
use Geokit\Position;

$southWest = Position::fromXY(2, 1);
$northEast = Position::fromXY(2, 1);

$boundingBox = BoundingBox::fromCornerPositions($southWest, $northEast);

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
use Geokit\Distance;

$expandedBoundingBox = $boundingBox->expand(
    Distance::fromString('10km')
);

$shrinkedBoundingBox = $boundingBox->shrink(
    Distance::fromString('10km')
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
use Geokit\BoundingBox;
use Geokit\Polygon;
use Geokit\Position;

$polygon = Polygon::fromPositions(
    Position::fromXY(0, 0),
    Position::fromXY(1, 0),
    Position::fromXY(1, 1)
);

$closedPolygon = $polygon->close();

/** @var Position $position */
foreach ($polygon as $position) {
}

$polygon->contains(Position::fromXY(0.5, 0.5)); // true

/** @var BoundingBox $boundingBox */
$boundingBox = $polygon->toBoundingBox();
```

### Functions

Geokit provides several functions to perform geographic calculations.

#### Distance calculations

* `distanceHaversine(Position $from, Position $to)`:
  Calculates the approximate sea level great circle (Earth) distance between two
  points using the Haversine formula.
* `distanceVincenty(Position $from, Position $to)`:
  Calculates the geodetic distance between two points using the Vincenty inverse
  formula for ellipsoids.

```php
use function Geokit\distanceHaversine;
use function Geokit\distanceVincenty;

$distance1 = distanceHaversine($from, $to);
$distance2 = distanceVincenty($from, $to);
```

Both functions return a [Distance](#distance) instance.

#### Transformations

The `circle()` function calculates a closed circle Polygon given a center,
radius and steps for precision.

```php
use Geokit\Distance;
use Geokit\Position;
use function Geokit\circle;

$circlePolygon = circle(
    Position::fromXY(8.50207515, 49.50042565), 
    Distance::fromString('5km'),
    32
);
```

#### Other calculations

Other useful functions are:

* `heading(Position $from, Position $to)`: Calculates the (initial) heading from
  the first point to the second point in degrees.
* `midpoint(Position $from, Position $to)`: Calculates an intermediate point on
  the geodesic between the two given points.
* `endpoint(Position $start, float $heading, Geokit\Distance $distance)`:
  Calculates the destination point along a geodesic, given an initial heading
  and distance, from the given start point.

License
-------

Copyright (c) 2011-2021 Jan Sorgalla.
Released under the [MIT License](LICENSE).
