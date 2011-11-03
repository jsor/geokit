<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Geometry;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class LineString extends MultiPoint
{
    /**
     * Constructor.
     *
     * @param array $points The Point array
     */
    public function __construct(array $points)
    {
        parent::__construct($points);

        if ($this->count() < 2) {
            throw new \InvalidArgumentException(
                sprintf('%s must have at least two points', $this->getGeometryType())
            );
        }
    }
}
