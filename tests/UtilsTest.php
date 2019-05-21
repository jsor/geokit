<?php

namespace Geokit;

class UtilsTest extends TestCase
{
    /**
     * @dataProvider normalizeLatDataProvider
     */
    public function testNormalizeLat(float $a, float $b): void
    {
        $this->assertEquals($b, Utils::normalizeLat($a));
    }

    public function normalizeLatDataProvider(): array
    {
        return [
            [-95, -85],
            [-90, -90],
            [5, 5],
            [90, 90],
            [100, 80],
            [180, 0]
        ];
    }

    /**
     * @dataProvider normalizeLngDataProvider
     */
    public function testNormalizeLng(float $a, float $b): void
    {
        $this->assertEquals($b, Utils::normalizeLng($a));
    }

    public function normalizeLngDataProvider(): array
    {
        return [
            [-545, 175],
            [-365, -5],
            [-360, 0],
            [-185, 175],
            [-180, 180],
            [5, 5],
            [180, 180],
            [215, -145],
            [360, 0],
            [395, 35],
            [540, 180]
        ];
    }
}
