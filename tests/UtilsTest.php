<?php

namespace Geokit;

class UtilsTest extends TestCase
{
    /**
     * @dataProvider normalizeLatDataProvider
     */
    public function testNormalizeLat($a, $b)
    {
        $this->assertEquals(Utils::normalizeLat($a), $b);
    }

    public function normalizeLatDataProvider()
    {
        return array(
            array(-95, -90),
            array(-90, -90),
            array(5, 5),
            array(90, 90),
            array(180, 90)
        );
    }

    /**
     * @dataProvider normalizeLngDataProvider
     */
    public function testNormalizeLng($a, $b)
    {
        $this->assertEquals(Utils::normalizeLng($a), $b);
    }

    public function normalizeLngDataProvider()
    {
        return array(
            array(-545, 175),
            array(-365, -5),
            array(-185, 175),
            array(-180, -180),
            array(5, 5),
            array(180, 180),
            array(215, -145),
            array(360, 0),
            array(395, 35),
            array(540, 180)
        );
    }
}
