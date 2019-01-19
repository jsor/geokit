<?php

namespace Geokit;

class EllipsoidTest extends TestCase
{
    public function testCreateFromSemiMajorInverseF()
    {
        $ellipsoid = Ellipsoid::createFromSemiMajorAndInvF(6378137.0, 298.257223563);

        $this->assertSame(6356752.3142451793, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106647474, $ellipsoid->getFlattening());
    }

    public function testCreateFromSemiMajorInverseFThrowsExceptionForInvFlatteongLTEZero()
    {
        $this->expectException(\InvalidArgumentException::class);

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

    public function testWgs84Factory()
    {
        $ellipsoid = Ellipsoid::wgs84();

        $this->assertSame(6378137.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356752.3142451793, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106647474805, $ellipsoid->getFlattening());
        $this->assertSame(298.257223563, $ellipsoid->getInverseFlattening());
    }

    public function testWgs72Factory()
    {
        $ellipsoid = Ellipsoid::wgs72();

        $this->assertSame(6378135.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356750.5200160937, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.003352779454167505, $ellipsoid->getFlattening());
        $this->assertSame(298.26, $ellipsoid->getInverseFlattening());
    }

    public function testWgs66Factory()
    {
        $ellipsoid = Ellipsoid::wgs66();

        $this->assertSame(6378145.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356759.7694886839, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528918692372171, $ellipsoid->getFlattening());
        $this->assertSame(298.25, $ellipsoid->getInverseFlattening());
    }

    public function testGrs80Factory()
    {
        $ellipsoid = Ellipsoid::grs80();

        $this->assertSame(6378137.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356752.3141403561, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528106811823188, $ellipsoid->getFlattening());
        $this->assertSame(298.257222101, $ellipsoid->getInverseFlattening());
    }

    public function testAnsFactory()
    {
        $ellipsoid = Ellipsoid::ans();

        $this->assertSame(6378160.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356774.7191953054, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033528918692372171, $ellipsoid->getFlattening());
        $this->assertSame(298.25, $ellipsoid->getInverseFlattening());
    }

    public function testAiry1830Factory()
    {
        $ellipsoid = Ellipsoid::airy1830();

        $this->assertSame(6377563.396, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356256.9092372851, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.0033408506414970775, $ellipsoid->getFlattening());
        $this->assertSame(299.3249646, $ellipsoid->getInverseFlattening());
    }

    public function testKrassovsky1940Factory()
    {
        $ellipsoid = Ellipsoid::krassovsky1940();

        $this->assertSame(6378245.0, $ellipsoid->getSemiMajorAxis());
        $this->assertSame(6356863.0187730473, $ellipsoid->getSemiMinorAxis());
        $this->assertSame(0.003352329869259135, $ellipsoid->getFlattening());
        $this->assertSame(298.3, $ellipsoid->getInverseFlattening());
    }
}
