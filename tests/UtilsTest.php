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
        return [
            [-95, -90],
            [-90, -90],
            [5, 5],
            [90, 90],
            [180, 90]
        ];
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
        return [
            [-545, 175],
            [-365, -5],
            [-185, 175],
            [-180, -180],
            [5, 5],
            [180, 180],
            [215, -145],
            [360, 0],
            [395, 35],
            [540, 180]
        ];
    }
}
