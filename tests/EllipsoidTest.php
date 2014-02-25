<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit;

/**
 * @covers Geokit\Ellipsoid
 */
class EllipsoidTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromSemiMajorInverseF()
    {
        $ellipsoid = Ellipsoid::createFromSemiMajorAndInvF(6378137.0, 298.257223563);

        $this->assertSame(6356752.3142451793, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106647474, $ellipsoid->getFlattening());
    }

    public function testCreateFromSemiMajorInverseFThrowsExceptionForInvFlatteongLTEZero()
    {
        $this->setExpectedException('\InvalidArgumentException');

        Ellipsoid::createFromSemiMajorAndInvF(0, 0);
    }

    public function testCreateFromSemiMajorAndSemiMinor()
    {
        $ellipsoid = Ellipsoid::createFromSemiMajorAndSemiMinor(6378137.0, 6356752.3142451793);

        $this->assertSame(0.0033528106647474, $ellipsoid->getFlattening());
        $this->assertSame(298.257223563, $ellipsoid->getInverseFlattening());
    }

    public function testGetter()
    {
        $ellipsoid = new Ellipsoid(1, 2, 3, 4);

        $this->assertSame(1.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(2.0, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(3.0, $ellipsoid->getFlattening());
        $this->assertSame(4.0, $ellipsoid->getInverseFlattening());
    }

    public function testArrayAccess()
    {
        $keys = array(
            'semi_major_axis',
            'semi_major',
            'semiMajorAxis',
            'semiMajor',
            'semi_minor_axis',
            'semi_minor',
            'semiMinorAxis',
            'semiMinor',
            'flattening',
            'f',
            'inverse_flattening',
            'inverse_f',
            'inv_f',
            'inverseFlattening',
            'inverseF',
            'invF'
        );

        $ellipsoid = new Ellipsoid(1, 2, 3, 4);

        foreach ($keys as $key) {
            $this->assertTrue(isset($ellipsoid[$key]));
            $this->assertNotNull($ellipsoid[$key]);
        }
    }

    public function testOffsetGetThrowsExceptionForInvalidKey()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid offset "foo".');

        $ellipsoid = new Ellipsoid(1, 2, 3, 4);

        $ellipsoid['foo'];
    }

    public function testOffsetSetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $ellipsoid = new Ellipsoid(1, 2, 3, 4);

        $ellipsoid['flattening'] = 5;
    }

    public function testOffsetUnsetThrowsException()
    {
        $this->setExpectedException('\BadMethodCallException');

        $ellipsoid = new Ellipsoid(1, 2, 3, 4);

        unset($ellipsoid['flattening']);
    }

    public function testWGS84()
    {
        $ellipsoid = Ellipsoid::WGS84();

        $this->assertSame(6378137.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356752.3142451793, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106647474, $ellipsoid->getFlattening());
        $this->assertSame(298.257223563, $ellipsoid->getInverseFlattening());
    }

    public function testNormalizeShouldAcceptEllipsoidArgument()
    {
        $ellipsoid1 = Ellipsoid::WGS84();
        $ellipsoid2 = Ellipsoid::normalize($ellipsoid1);

        $this->assertEquals($ellipsoid1, $ellipsoid2);
    }

    public function testNormalizeShouldReturnWGS84ForNullArgument()
    {
        $ellipsoid = Ellipsoid::normalize(null);

        $this->assertSame(6378137.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356752.3142451793, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106647474, $ellipsoid->getFlattening());
        $this->assertSame(298.257223563, $ellipsoid->getInverseFlattening());
    }

    /**
     * @dataProvider testNormalizeShouldAcceptArrayArgumentDataProvider
     */
    public function testNormalizeShouldAcceptArrayArgument($array)
    {
        $ellipsoid = Ellipsoid::normalize($array);

        $this->assertSame(6378137.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356752.3142451793, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106647474, $ellipsoid->getFlattening());
        $this->assertSame(298.257223563, $ellipsoid->getInverseFlattening());
    }

    public function testNormalizeShouldAcceptArrayArgumentDataProvider()
    {
        $semiMajorAxisKeys = array(
            'semi_major_axis',
            'semi_major',
            'semiMajorAxis',
            'semiMajor',
        );

        $semiMinorAxisKeys = array(
            'semi_minor_axis',
            'semi_minor',
            'semiMinorAxis',
            'semiMinor',
        );

        $inverseFlatteningKeys = array(
            'inverse_flattening',
            'inverse_f',
            'inv_f',
            'inverseFlattening',
            'inverseF',
            'invF'
        );

        $data = array();

        foreach ($semiMajorAxisKeys as $semiMajorAxisKey) {
            foreach ($semiMinorAxisKeys as $semiMinorAxisKey) {
                $data[] = array(
                    array(
                        $semiMajorAxisKey => 6378137.0,
                        $semiMinorAxisKey => 6356752.3142451793
                    )
                );
            }

            foreach ($inverseFlatteningKeys as $inverseFlatteningKey) {
                $data[] = array(
                    array(
                        $semiMajorAxisKey => 6378137.0,
                        $inverseFlatteningKey => 298.257223563
                    )
                );
            }
        }

        $data[] = array(
            array(
                6378137.0,
                298.257223563
            )
        );

        return $data;
    }

    public function testNormalizeShouldThrowExceptionForInvalidArrayInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize Ellipsoid from input ["foo",""].');
        Ellipsoid::normalize(array('foo', ''));
    }

    public function testNormalizeShouldThrowExceptionForInvalidStringInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize Ellipsoid from input "foo".');
        Ellipsoid::normalize('foo');
    }

    public function testNormalizeShouldThrowExceptionForInvalidObjectInput()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Cannot normalize Ellipsoid from input {}.');
        Ellipsoid::normalize(new \stdClass());
    }
}
