<?php

namespace Geokit;

class BoundsTest extends TestCase
{
    /**
     * @param Bounds  $b
     * @param integer $w
     * @param integer $s
     * @param integer $e
     * @param integer $n
     */
    protected function assertBounds(Bounds $b, $s, $w, $n, $e)
    {
        $this->assertEquals($s, $b->getSouthWest()->getLatitude());
        $this->assertEquals($w, $b->getSouthWest()->getLongitude());
        $this->assertEquals($n, $b->getNorthEast()->getLatitude());
        $this->assertEquals($e, $b->getNorthEast()->getLongitude());
    }

    public function testConstructorShouldAcceptLatLngsAsFirstAndSecondArgument()
    {
        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $this->assertInstanceOf('\Geokit\LatLng', $bounds->getSouthWest());
        $this->assertEquals(1.1234, $bounds->getSouthWest()->getLongitude());
        $this->assertEquals(2.5678, $bounds->getSouthWest()->getLatitude());

        $this->assertInstanceOf('\Geokit\LatLng', $bounds->getNorthEast());
        $this->assertEquals(3.1234, $bounds->getNorthEast()->getLongitude());
        $this->assertEquals(4.5678, $bounds->getNorthEast()->getLatitude());
    }

    public function testConstructorShouldThrowExceptionForInvalidSouthCoordinate()
    {
        $this->expectException(\LogicException::class);
        new Bounds(new LatLng(1, 90), new LatLng(0, 90));
    }

    public function testGetCenterShouldReturnALatLngObject()
    {
        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));
        $center = new LatLng(3.5678, 2.1234);

        $this->assertEquals($center, $bounds->getCenter());

        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));
        $center = new LatLng(0, 180);

        $this->assertEquals($center, $bounds->getCenter());
    }

    public function testGetSpanShouldReturnALatLngObject()
    {
        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $span = new LatLng(2, 2);

        $this->assertEquals($span, $bounds->getSpan());
    }

    public function testArrayAccess()
    {
        $keys = array(
            'southwest',
            'south_west',
            'southWest',
            'northeast',
            'north_east',
            'northEast',

            'center',
            'span'
        );

        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $bounds);
            $this->assertNotNull($bounds[$key]);
        }
    }

    public function testOffsetGetThrowsExceptionForInvalidKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid offset "foo".');

        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $bounds['foo'];
    }

    public function testOffsetSetThrowsException()
    {
        $this->expectException(\BadMethodCallException::class);

        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        $bounds['southwest'] = 5;
    }

    public function testOffsetUnsetThrowsException()
    {
        $this->expectException(\BadMethodCallException::class);

        $bounds = new Bounds(new LatLng(2.5678, 1.1234), new LatLng(4.5678, 3.1234));

        unset($bounds['southwest']);
    }

    public function testContains()
    {
        $bounds = new Bounds(new LatLng(-122, 37), new LatLng(-123, 38));

        $this->assertTrue($bounds->contains(new LatLng(-122, 37)));
        $this->assertTrue($bounds->contains(new LatLng(-123, 38)));

        $this->assertFalse($bounds->contains(new LatLng(-70, -12)));
    }

    public function testExtendInACircle()
    {
        $bounds = new Bounds(new LatLng(0, 0), new LatLng(0, 0));
        $bounds = $bounds->extend(new LatLng(0, 1));
        $bounds = $bounds->extend(new LatLng(1, 0));
        $bounds = $bounds->extend(new LatLng(0, -1));
        $bounds = $bounds->extend(new LatLng(-1, 0));
        $this->assertBounds($bounds, -1, -1, 1, 1);
    }

    public function testUnion()
    {
        $bounds = new Bounds(new LatLng(37, -122), new LatLng(37, -122));

        $bounds = $bounds->union(new Bounds(new LatLng(-38, 123), new LatLng(38, -123)));
        $this->assertBounds($bounds, -38, 123, 38, -122);
    }

    public function testCrossesAntimeridian()
    {
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testCrossesAntimeridianViaExtend()
    {
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));

        $bounds = $bounds->extend(new LatLng(90, -180));

        $this->assertTrue($bounds->crossesAntimeridian());
        $this->assertEquals(90, $bounds->getSpan()->getLatitude());
        $this->assertEquals(2, $bounds->getSpan()->getLongitude());
    }

    public function testNormalizeShouldAcceptBoundsArgument()
    {
        $bounds1 = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));
        $bounds2 = Bounds::normalize($bounds1);

        $this->assertEquals($bounds1, $bounds2);
    }

    public function testNormalizeShouldAcceptStringArgument()
    {
        $bounds = Bounds::normalize('-45 179 45 -179');
        $this->assertBounds($bounds, -45, 179, 45, -179);

        $bounds = Bounds::normalize('-45, 179, 45, -179');
        $this->assertBounds($bounds, -45, 179, 45, -179);
    }

    /**
     * @dataProvider normalizeShouldAcceptArrayArgumentDataProvider
     */
    public function testNormalizeShouldAcceptArrayArgument($array)
    {
        $bounds = Bounds::normalize($array);
        $this->assertBounds($bounds, -45, 179, 45, -179);
    }

    public function normalizeShouldAcceptArrayArgumentDataProvider()
    {
        $southWestKeys = array(
            'southwest',
            'south_west',
            'southWest',
        );

        $northEastKeys = array(
            'northeast',
            'north_east',
            'northEast',
        );

        $data = array();

        foreach ($southWestKeys as $southWestKey) {
            foreach ($northEastKeys as $northEastKey) {
                $data[] = array(
                    array(
                        $southWestKey => array(-45, 179),
                        $northEastKey => array(45, -179)
                    )
                );
            }
        }

        $data[] = array(
            array(
                array(-45, 179),
                array(45, -179)
            )
        );

        return $data;
    }

    public function testNormalizeShouldThrowExceptionForInvalidArrayInput()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot normalize Bounds from input ["foo",""].');
        Bounds::normalize(array('foo', ''));
    }

    public function testNormalizeShouldThrowExceptionForInvalidStringInput()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot normalize Bounds from input "foo".');
        Bounds::normalize('foo');
    }

    public function testNormalizeShouldThrowExceptionForInvalidObjectInput()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot normalize Bounds from input {}.');
        Bounds::normalize(new \stdClass());
    }

    /**
     * @dataProvider offsetExistsDataProvider
     */
    public function testOffsetExists($existedKey)
    {
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));

        $this->assertTrue($bounds->offsetExists($existedKey));
    }

    public function offsetExistsDataProvider()
    {
        return array(
            array('southwest'),
            array('south_west'),
            array('southWest'),
            array('northeast'),
            array('north_east'),
            array('northEast'),
            array('center'),
            array('span')
        );
    }

    public function testOffsetExistsOnNonExistedKeys()
    {
        $bounds = new Bounds(new LatLng(-45, 179), new LatLng(45, -179));

        $this->assertFalse($bounds->offsetExists('non_existed_key'));
    }
}
