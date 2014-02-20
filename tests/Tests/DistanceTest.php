<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests;

use Geokit\Distance;

/**
 * @covers Geokit\Distance
 */
class DistanceTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldConvertToMeters()
    {
        $distance = new Distance(1000);
        $this->assertSame(1000.0, $distance->meters());
    }

    public function testShouldConvertToMetersWithAlias()
    {
        $distance = new Distance(1000);
        $this->assertSame(1000.0, $distance->m());
    }

    public function testShouldConvertToKilometers()
    {
        $distance = new Distance(1000);
        $this->assertSame(1.0, $distance->kilometers());
    }

    public function testShouldConvertToKilometersWithAlias()
    {
        $distance = new Distance(1000);
        $this->assertSame(1.0, $distance->km());
    }

    public function testShouldConvertToMiles()
    {
        $distance = new Distance(1000);
        $this->assertSame(0.62137119223733395, $distance->miles());
    }

    public function testShouldConvertToMilesWithAlias()
    {
        $distance = new Distance(1000);
        $this->assertSame(0.62137119223733395, $distance->mi());
    }

    public function testShouldConvertToFeet()
    {
        $distance = new Distance(1000);
        $this->assertSame(3280.8398950131232, $distance->feet());
    }

    public function testShouldConvertToFeetWithAlias()
    {
        $distance = new Distance(1000);
        $this->assertSame(3280.8398950131232, $distance->ft());
    }

    public function testShouldConvertToNauticalMiles()
    {
        $distance = new Distance(1000);
        $this->assertSame(0.5399568034557235, $distance->nautical());
    }

    public function testShouldConvertToNauticalWithAlias()
    {
        $distance = new Distance(1000);
        $this->assertSame(0.5399568034557235, $distance->nm());
    }
}
