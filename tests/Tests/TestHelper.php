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

/*
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 */
class TestHelper
{
    public static function createBuzzResponseFromString($str)
    {
        $response = new \Buzz\Message\Response();

        $lines = preg_split('/(\\r?\\n)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i = 0, $count = count($lines); $i < $count; $i += 2) {
            $line = $lines[$i];
            if (empty($line)) {
                $response->setContent(implode('', array_slice($lines, $i + 2)));
                break;
            }

            $response->addHeader($line);
        }

        return $response;
    }
}
