Geokit
======

What is Geokit?
---------------

Geokit is a PHP 5.3+ toolkit to solve geo-related tasks like geometric calculations, geocoding etc.

Installation
------------

Geokit can be installed using the [Composer](http://packagist.org/) tool. You can either add `geokit/geokit` to your package dependencies, or if you want to install Geokit as standalone, go to the main directory of this package and run:

    $ wget http://getcomposer.org/composer.phar 
    $ php composer.phar install

You can then use the composer-generated autoloader to access the Geokit classes:

    require 'vendor/.composer/autoload.php';

License
-------

Geokit is released under the [MIT License](https://github.com/jsor/Geokit/blob/master/LICENSE).

Credits
-------

Some parts have been ported from the [OpenLayers](https://github.com/openlayers/openlayers)
and [geojs](http://code.google.com/p/geojs) libraries.
