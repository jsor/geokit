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

use Geokit\Geometry\Transformer\MySQLWKBTransformer;
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
 * @covers Geokit\Geometry\Transformer\MySQLWKBTransformer
 */
class MySQLWKBTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group database
     * @group mysql
     * @dataProvider geometryDataProvider
     */
    public function testTransform($a, $b, $message)
    {
        $transformer = new MySQLWKBTransformer();

        $this->assertEquals($a, $transformer->transform($b), sprintf($message, 'transform()'));
    }

    /**
     * @group database
     * @group mysql
     * @dataProvider geometryDataProvider
     */
    public function testReverseTransform($a, $b, $message)
    {
        $transformer = new MySQLWKBTransformer();

        $this->assertEquals($b, $transformer->reverseTransform($a), sprintf($message, 'reverseTransform()'));
    }

    public function geometryDataProvider()
    {
        $pdo = new \PDO(DB_MYSQL_DSN, DB_MYSQL_USER, DB_MYSQL_PASSWD);

        $pdo->exec("CREATE TABLE `geometry` (
                    `Point` POINT NOT NULL ,
                    `MultiPoint` MULTIPOINT NOT NULL ,
                    `LineString` LINESTRING NOT NULL ,
                    `MultiLineString` MULTILINESTRING NOT NULL ,
                    `Polygon` POLYGON NOT NULL ,
                    `MultiPolygon` MULTIPOLYGON NOT NULL ,
                    `GeometryCollection` GEOMETRYCOLLECTION NOT NULL
                    )");

        $data = array();

        $point = new Point(1, 1);
        $multipoint = new MultiPoint(
            array(
                new Point(1, 2),
                new Point(3, 4)
            )
        );
        $linestring = new LineString(
            array(
                new Point(1, 2),
                new Point(3, 4)
            )
        );
        $multilinestring = new MultiLineString(
            array(
                new LineString(
                    array(
                        new Point(1, 2),
                        new Point(3, 4)
                    )
                ),
                new LineString(
                    array(
                        new Point(5, 6),
                        new Point(7, 8)
                    )
                )
            )
        );
        $polygon = new Polygon(
            array(
                new LinearRing(
                    array(
                        new Point(1, 2),
                        new Point(3, 4),
                        new Point(5, 6),
                        new Point(7, 8)
                    )
                ),
                new LinearRing(
                    array(
                        new Point(9, 10),
                        new Point(11, 12),
                        new Point(13, 14),
                        new Point(15, 16)
                    )
                )
            )
        );
        $multipolygon = new MultiPolygon(
            array(
                new Polygon(
                    array(
                        new LinearRing(
                            array(
                                new Point(1, 2),
                                new Point(3, 4),
                                new Point(5, 6),
                                new Point(7, 8)
                            )
                        ),
                        new LinearRing(
                            array(
                                new Point(9, 10),
                                new Point(11, 12),
                                new Point(13, 14),
                                new Point(15, 16)
                            )
                        )
                    )
                ),
                new Polygon(
                    array(
                        new LinearRing(
                            array(
                                new Point(17, 18),
                                new Point(19, 20),
                                new Point(21, 22),
                                new Point(23, 24)
                            )
                        ),
                        new LinearRing(
                            array(
                                new Point(25, 26),
                                new Point(27, 28),
                                new Point(29, 30),
                                new Point(31, 32)
                            )
                        )
                    )
                )
            )
        );
        $geometrycollection = new GeometryCollection(
            array(
                new Point(1, 2),
                new LineString(
                    array(
                        new Point(3, 4),
                        new Point(5, 6),
                        new Point(7, 8)
                    )
                ),
            )
        );

        $sql = "INSERT INTO `geometry`
                (`Point`,
                `MultiPoint`,
                `LineString`,
                `MultiLineString`,
                `Polygon`,
                `MultiPolygon`,
                `GeometryCollection`)
                VALUES
                (GeomFromText(?),
                GeomFromText(?),
                GeomFromText(?),
                GeomFromText(?),
                GeomFromText(?),
                GeomFromText(?),
                GeomFromText(?))";

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array(
            (string) $point,
            (string) $multipoint,
            (string) $linestring,
            (string) $multilinestring,
            (string) $polygon,
            (string) $multipolygon,
            (string) $geometrycollection
        ));

        $sql = "SELECT
                `Point`,
                `MultiPoint`,
                `LineString`,
                `MultiLineString`,
                `Polygon`,
                `MultiPolygon`,
                `GeometryCollection`
                FROM `geometry`";

        $row = $pdo->query($sql)->fetch(\PDO::FETCH_ASSOC);

        $data[] = array(
            $row['Point'],
            $point,
            "%s correctly processes Point"
        );

        $data[] = array(
            $row['MultiPoint'],
            $multipoint,
            "%s correctly processes MultiPoint"
        );

        $data[] = array(
            $row['LineString'],
            $linestring,
            "%s correctly processes LineString"
        );

        $data[] = array(
            $row['MultiLineString'],
            $multilinestring,
            "%s correctly processes MultiLineString"
        );

        $data[] = array(
            $row['Polygon'],
            $polygon,
            "%s correctly processes Polygon"
        );

        $data[] = array(
            $row['MultiPolygon'],
            $multipolygon,
            "%s correctly processes MultiPolygon"
        );

        $data[] = array(
            $row['GeometryCollection'],
            $geometrycollection,
            "%s correctly processes GeometryCollection"
        );

        $pdo->exec('DROP TABLE `geometry`');
        unset($pdo);

        return $data;
    }
}
