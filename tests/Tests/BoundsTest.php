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

use Geokit\Bounds;
use Geokit\LngLat;

/**
 * @covers Geokit\Bounds
 */
class BoundsTest extends \PHPUnit_Framework_TestCase
{
    protected function assertBounds(Bounds $b, $w, $s, $e, $n)
    {
        $this->assertEquals($w, $b->getWestSouth()->getLongitude());
        $this->assertEquals($s, $b->getWestSouth()->getLatitude());
        $this->assertEquals($e, $b->getEastNorth()->getLongitude());
        $this->assertEquals($n, $b->getEastNorth()->getLatitude());
    }

    public function testConstructorShouldAcceptLngLatsAsFirstAndSecondArgument()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $this->assertTrue($bounds->getWestSouth() instanceof LngLat);
        $this->assertEquals(1.1234, $bounds->getWestSouth()->getLongitude());
        $this->assertEquals(2.5678, $bounds->getWestSouth()->getLatitude());

        $this->assertTrue($bounds->getEastNorth() instanceof LngLat);
        $this->assertEquals(3.1234, $bounds->getEastNorth()->getLongitude());
        $this->assertEquals(4.5678, $bounds->getEastNorth()->getLatitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate()
    {
        $this->setExpectedException('\LogicException');
        new Bounds(new LngLat(90, 1), new LngLat(90, 0));
    }

    public function testGetCenterShouldReturnALngLatObject()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));
        $center = new LngLat(2.1234, 3.5678);

        $this->assertEquals($center, $bounds->getCenter());

        $bounds = new Bounds(new LngLat(179, -45), new LngLat(-179, 45));
        $center = new LngLat(180, 0);

        $this->assertEquals($center, $bounds->getCenter());
    }

    public function testGetSpanShouldReturnALngLatObject()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $span = new LngLat(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testArrayAccess()
    {
        $keys = array(
            'westsouth',
            'west_south',
            'westSouth',
            'eastnorth',
            'east_north',
            'eastNorth',

            'center',
            'span'
        );

        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        foreach ($keys as $key) {
            $this->assertTrue(isset($bounds[$key]));
            $this->assertNotNull($bounds[$key]);
        }
    }

    public function testOffsetGetThrowsExceptionForInvalidKey()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid offset "foo".');

        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $bounds['foo'];
    }

    public function testOffsetSetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $bounds['southwest'] = 5;
    }

    public function testOffsetUnsetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        unset($bounds['southwest']);
    }

    public function testContainsLngLat()
    {
        $bounds = new Bounds(new LngLat(1.1234, 2.5678), new LngLat(3.1234, 4.5678));

        $span = new LngLat(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testExtendByLngLat()
    {
        $bounds = new Bounds(new LngLat(37, -122), new LngLat(38, -123));

        $this->assertTrue($bounds->containsLngLat(new LngLat(37, -122)));
        $this->assertTrue($bounds->containsLngLat(new LngLat(38, -123)));

        $this->assertFalse($bounds->containsLngLat(new LngLat(-12, -70)));
    }

    public function testExtendByLngLatInACircle()
    {
        $bounds = new Bounds(new LngLat(0, 0), new LngLat(0, 0));
        $bounds = $bounds->extendByLngLat(new LngLat(1, 0));
        $bounds = $bounds->extendByLngLat(new LngLat(0, 1));
        $bounds = $bounds->extendByLngLat(new LngLat(-1, 0));
        $bounds = $bounds->extendByLngLat(new LngLat(0, -1));
        $this->assertBounds($bounds, -1, -1, 1, 1);
    }

    public function testExtendByBounds()
    {
        $bounds = new Bounds(new LngLat(-122, 37), new LngLat(-122, 37));

        $bounds = $bounds->extendByBounds(new Bounds(new LngLat(123, -38), new LngLat(-123, 38)));
        $this->assertBounds($bounds, 123, -38, -122, 38);
    }

    public function testCrossesAntimeridian()
    {
        $bounds = new Bounds(new LngLat(179, -45), new LngLat(-179, 45));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testCrossesAntimeridianViaExtend()
    {
        $bounds = new Bounds(new LngLat(179, -45), new LngLat(-179, 45));

        $bounds = $bounds->extendByLngLat(new LngLat(-180, 90));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testNormalizeShouldAcceptBoundsArgument()
    {
        $bounds1 = new Bounds(new LngLat(179, -45), new LngLat(-179, 45));
        $bounds2 = Bounds::normalize($bounds1);

        $this->assertEquals($bounds1, $bounds2);
    }

    public function testNormalizeShouldAcceptStringArgument()
    {
        $bounds = Bounds::normalize('179 -45 -179 45');
        $this->assertBounds($bounds, 179, -45, -179, 45);

        $bounds = Bounds::normalize('179, -45, -179, 45');
        $this->assertBounds($bounds, 179, -45, -179, 45);
    }

    /**
     * @dataProvider testNormalizeShouldAcceptArrayArgumentDataProvider
     */
    public function testNormalizeShouldAcceptArrayArgument($array)
    {
        $bounds = Bounds::normalize($array);
        $this->assertBounds($bounds, 179, -45, -179, 45);
    }

    public function testNormalizeShouldAcceptArrayArgumentDataProvider()
    {
        $westSouthKeys = array(
            'westsouth',
            'west_south',
            'westSouth'
        );

        $eastNorthKeys = array(
            'eastnorth',
            'east_north',
            'eastNorth'
        );

        $data = array();

        foreach ($westSouthKeys as $westSouthKey) {
            foreach ($eastNorthKeys as $eastNorthKey) {
                $data[] = array(
                    array(
                        $westSouthKey => array(179, -45),
                        $eastNorthKey => array(-179, 45)
                    )
                );
            }
        }

        $data[] = array(
            array(
                array(179, -45),
                array(-179, 45)
            )
        );

        return $data;
    }

    public function testNormalizeShouldThrowExceptionForInvalidArrayInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize Bounds from input ["foo",""].');
        Bounds::normalize(array('foo', ''));
    }

    public function testNormalizeShouldThrowExceptionForInvalidStringInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize Bounds from input "foo".');
        Bounds::normalize('foo');
    }

    public function testNormalizeShouldThrowExceptionForInvalidObjectInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize Bounds from input {}.');
        Bounds::normalize(new \stdClass());
    }
}