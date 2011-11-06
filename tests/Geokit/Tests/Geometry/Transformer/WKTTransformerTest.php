<?php

/*
 * This file is part of Geokit.
 *
 * (c) Jan Sorgalla <jsorgalla@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Geokit\Tests\Geometry\Transformer;

use Geokit\Geometry\Transformer\WKTTransformer;
use Geokit\Geometry\GeometryCollection;
use Geokit\Geometry\LineString;
use Geokit\Geometry\LinearRing;
use Geokit\Geometry\MultiLineString;
use Geokit\Geometry\MultiPoint;
use Geokit\Geometry\MultiPolygon;
use Geokit\Geometry\Point;
use Geokit\Geometry\Polygon;

/**
 * @author  Jan Sorgalla <jsorgalla@googlemail.com>
 * @version @package_version@
 *
 * @covers Geokit\Geometry\Transformer\WKTTransformer
 */
class WKTTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider geometryDataProvider
     */
    public function testTransform($a, $b, $message)
    {
        $transformer = new WKTTransformer();

        $this->assertEquals($a, $transformer->transform($b), sprintf($message, 'transform()'));
    }

    /**
     * @dataProvider geometryDataProvider
     */
    public function testReverseTransform($a, $b, $message)
    {
        $transformer = new WKTTransformer();

        $this->assertEquals($b, $transformer->reverseTransform($a), sprintf($message, 'reverseTransform()'));
    }

    public function geometryDataProvider()
    {
        $points = array();
        for ($i = 0; $i < 12; $i++) {
            $points[] = new Point(mt_rand(0, 100), mt_rand(0, 100));
        }

        $multipoint = new MultiPoint(array(
            $points[0],
            $points[1],
            $points[2]
        ));

        $linestrings = array(
            new LineString(array(
                $points[0],
                $points[1],
                $points[2]
            )),
            new LineString(array(
                $points[3],
                $points[4],
                $points[5]
            ))
        );

        $multilinestring = new MultiLineString(array(
            $linestrings[0],
            $linestrings[1]
        ));

        $rings = array(
            new LinearRing(array(
                $points[0],
                $points[1],
                $points[2]
            )),
            new LinearRing(array(
                $points[3],
                $points[4],
                $points[5]
            )),
            new LinearRing(array(
                $points[6],
                $points[7],
                $points[8]
            )),
            new LinearRing(array(
                $points[9],
                $points[10],
                $points[11]
            ))
        );

        $polygons = array(
            new Polygon(array($rings[0], $rings[1])),
            new Polygon(array($rings[2], $rings[3]))
        );

        $multipolygon = new MultiPolygon(array(
            $polygons[0],
            $polygons[1]
        ));

        $geometrycollection = new GeometryCollection(array(
            $points[0],
            $linestrings[0]
        ));

        // --------------------

        $data = array();

        $data[] = array(
            sprintf(
                "POINT(%F %F)",
                $points[0]->getX(),
                $points[0]->getY()
            ),
            $points[0],
            "%s correctly processes Point"
        );

        $data[] = array(
            sprintf(
                "MULTIPOINT((%F %F),(%F %F),(%F %F))",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY()
            ),
            $multipoint,
            "%s correctly processes MultiPoint"
        );

        $data[] = array(
            sprintf(
                "LINESTRING(%F %F,%F %F,%F %F)",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY()
            ),
            $linestrings[0],
            "%s correctly processes LineString"
        );

        $data[] = array(
            sprintf(
                "MULTILINESTRING((%F %F,%F %F,%F %F),(%F %F,%F %F,%F %F))",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY(),
                $points[3]->getX(),
                $points[3]->getY(),
                $points[4]->getX(),
                $points[4]->getY(),
                $points[5]->getX(),
                $points[5]->getY()
            ),
            $multilinestring,
            "%s correctly processes MultiLineString"
        );

        $data[] = array(
            sprintf(
                "POLYGON((%F %F,%F %F,%F %F,%F %F),(%F %F,%F %F,%F %F,%F %F))",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY(),
                $points[0]->getX(),
                $points[0]->getY(),
                $points[3]->getX(),
                $points[3]->getY(),
                $points[4]->getX(),
                $points[4]->getY(),
                $points[5]->getX(),
                $points[5]->getY(),
                $points[3]->getX(),
                $points[3]->getY()
            ),
            $polygons[0],
            "%s correctly processes Polygon"
        );

        $data[] = array(
            sprintf(
                "MULTIPOLYGON(((%F %F,%F %F,%F %F,%F %F),(%F %F,%F %F,%F %F,%F %F)),((%F %F,%F %F,%F %F,%F %F),(%F %F,%F %F,%F %F,%F %F)))",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY(),
                $points[0]->getX(),
                $points[0]->getY(),
                $points[3]->getX(),
                $points[3]->getY(),
                $points[4]->getX(),
                $points[4]->getY(),
                $points[5]->getX(),
                $points[5]->getY(),
                $points[3]->getX(),
                $points[3]->getY(),
                $points[6]->getX(),
                $points[6]->getY(),
                $points[7]->getX(),
                $points[7]->getY(),
                $points[8]->getX(),
                $points[8]->getY(),
                $points[6]->getX(),
                $points[6]->getY(),
                $points[9]->getX(),
                $points[9]->getY(),
                $points[10]->getX(),
                $points[10]->getY(),
                $points[11]->getX(),
                $points[11]->getY(),
                $points[9]->getX(),
                $points[9]->getY()
            ),
            $multipolygon,
            "%s correctly processes MultiPolygon"
        );

        $data[] = array(
            sprintf(
                "GEOMETRYCOLLECTION(POINT(%F %F),LINESTRING(%F %F,%F %F,%F %F))",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY()
            ),
            $geometrycollection,
            "%s correctly processes GeometryCollection"
        );

        return $data;
    }

    public function testMultipointWithoutPointParens()
    {
        $transformer = new WKTTransformer();

        $points = array();
        for ($i = 0; $i < 3; $i++) {
            $points[] = new Point(mt_rand(0, 100), mt_rand(0, 100));
        }

        $multipoint = new MultiPoint(array(
            $points[0],
            $points[1],
            $points[2]
        ));

        $str = sprintf(
                "MULTIPOINT(%F %F,%F %F,%F %F)",
                $points[0]->getX(),
                $points[0]->getY(),
                $points[1]->getX(),
                $points[1]->getY(),
                $points[2]->getX(),
                $points[2]->getY()
            );

        $this->assertEquals($multipoint, $transformer->reverseTransform($str), 'reverseTransform() correctly processes MultiPoint without parens around Points');
    }

    public function testUnknownGeometry()
    {
        $transformer = new WKTTransformer();
        $this->assertNull($transformer->transform(new \Geokit\Tests\Geometry\Fixtures\TestGeometry1()), 'transform() returns NULL for unknown geometry');

        $this->assertNull($transformer->reverseTransform('(1 2)'), 'reverseTransform() returns NULL for undefined geometry');
        $this->assertNull($transformer->reverseTransform('DummGeometry(1 2)'), 'reverseTransform() returns NULL for unknown geometry');
    }

    public function testWhitespace()
    {
        $wkt = "LINESTRING(7.120068\t43.583917,\n7.120154 43.583652,\n7.120385\t43.582716,\r\n7.12039 43.582568, 7.120712 43.581511,7.120873\n43.580718)";
        $transformer = new WKTTransformer();
        $geometry = $transformer->reverseTransform($wkt);
        $this->assertInstanceOf('\Geokit\Geometry\LineString', $geometry);
        $this->assertCount(6, $geometry);
    }
}
