<?php

namespace Geokit;

class DistanceTest extends TestCase
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
        $this->expectException('\InvalidArgumentException');
        new Distance(1000, 'foo');
    }

    /**
     * @dataProvider fromStringDataProvider
     */
    public function testFromString($value, $unit)
    {
        $this->assertEquals(1000, Distance::fromString(\sprintf('%.15F%s', $value, $unit))->meters());
        $this->assertEquals(1000, Distance::fromString(\sprintf('%.15F %s', $value, $unit))->meters(), 'With space');
    }

    public function fromStringDataProvider()
    {
        return [
            [
                1000,
                ''
            ],
            [
                1000,
                'm'
            ],
            [
                1000,
                'meter'
            ],
            [
                1000,
                'meters'
            ],
            [
                1000,
                'metre'
            ],
            [
                1000,
                'metres'
            ],
            [
                1,
                'km'
            ],
            [
                1,
                'kilometer'
            ],
            [
                1,
                'kilometers'
            ],
            [
                1,
                'kilometre'
            ],
            [
                1,
                'kilometres'
            ],
            [
                0.62137119223733,
                'mi'
            ],
            [
                0.62137119223733,
                'mile'
            ],
            [
                0.62137119223733,
                'miles'
            ],
            [
                3280.83989501312336,
                'ft'
            ],
            [
                3280.83989501312336,
                'foot'
            ],
            [
                3280.83989501312336,
                'feet'
            ],
            [
                0.53995680345572,
                'nm'
            ],
            [
                0.53995680345572,
                'nautical'
            ],
            [
                0.53995680345572,
                'nauticalmile'
            ],
            [
                0.53995680345572,
                'nauticalmiles'
            ],
        ];
    }

    public function testFromStringThrowsExceptionForInvalidInput()
    {
        $this->expectException('\InvalidArgumentException');
        Distance::fromString('1000foo');
    }
}
