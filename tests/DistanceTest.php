<?php

namespace Geokit;

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

    public function testShouldThrowExceptionForInvalidUnit()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Distance(1000, 'foo');
    }

    public function testNormalizeShouldAcceptDistanceArgument()
    {
        $distance1 = new Distance(1000);
        $distance2 = Distance::normalize($distance1);

        $this->assertEquals($distance1, $distance2);
    }

    public function testNormalizeShouldAcceptFloatArgument()
    {
        $distance = Distance::normalize(1000.0);

        $this->assertEquals(1000, $distance->meters());
    }

    public function testNormalizeShouldAcceptNumericStringArgument()
    {
        $distance = Distance::normalize('1000.0');

        $this->assertEquals(1000, $distance->meters());
    }

    /**
     * @dataProvider normalizeShouldAcceptStringArgumentDataProvider
     */
    public function testNormalizeShouldAcceptStringArgument($value, $unit)
    {
        $this->assertEquals(1000, Distance::normalize(sprintf('%.15F%s', $value, $unit))->meters());
        $this->assertEquals(1000, Distance::normalize(sprintf('%.15F %s', $value, $unit))->meters(), 'With space');
    }

    public function normalizeShouldAcceptStringArgumentDataProvider()
    {
        return array(
            array(
                1000,
                'm'
            ),
            array(
                1000,
                'meter'
            ),
            array(
                1000,
                'meters'
            ),
            array(
                1000,
                'metre'
            ),
            array(
                1000,
                'metres'
            ),
            array(
                1,
                'km'
            ),
            array(
                1,
                'kilometer'
            ),
            array(
                1,
                'kilometers'
            ),
            array(
                1,
                'kilometre'
            ),
            array(
                1,
                'kilometres'
            ),
            array(
                0.62137119223733,
                'mi'
            ),
            array(
                0.62137119223733,
                'mile'
            ),
            array(
                0.62137119223733,
                'miles'
            ),
            array(
                3280.83989501312336,
                'ft'
            ),
            array(
                3280.83989501312336,
                'foot'
            ),
            array(
                3280.83989501312336,
                'feet'
            ),
            array(
                0.53995680345572,
                'nm'
            ),
            array(
                0.53995680345572,
                'nautical'
            ),
            array(
                0.53995680345572,
                'nauticalmile'
            ),
            array(
                0.53995680345572,
                'nauticalmiles'
            ),
        );
    }

    public function testNormalizeShouldThrowExceptionForInvalidInput()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Distance::normalize('1000foo');
    }

    public function testResolveUnitAliasShouldThrowExceptionForInvalidAlias()
    {
        $this->setExpectedException('\InvalidArgumentException');
        Distance::resolveUnitAlias('foo');
    }
}
