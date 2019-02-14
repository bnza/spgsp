<?php

namespace Bnza\SPgSp\Tests\ORM\Query\AST\Functions;

use Bnza\SPgSp\Tests\Fixtures\GeometryEntity;
use Bnza\SPgSp\ORM\Query\AST\Functions\StSrid;

class StSridTest extends AbstractFunctionTest
{
    protected function getASTFunctions(): array
    {
        return [
            'ST_SRID' => [
                'class' => StSrid::class,
                'type' => 'numeric',
            ],
        ];
    }

    public function getGeoJsonData(): array
    {
        return [
            [
                <<<EOF
            { 
                "type": "Point",
                "crs":{"type":"name","properties":{"name":"EPSG:4324"}},
                "coordinates": [100.0, 0.0] 
            }
EOF
                , 4324,
            ],
            [
                <<<EOF
            { 
                "type": "MultiPoint",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
                , 4326,
            ],
            [
                <<<EOF
            { 
                "type": "LineString",
                "crs":{"type":"name","properties":{"name":"EPSG:4322"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
                , 4322,
            ],
            [
                <<<EOF
            { 
                "type": "MultiLineString",
                "crs":{"type":"name","properties":{"name":"EPSG:4978"}},
                "coordinates": [
                    [ [100.0, 0.0], [101.0, 1.0] ],
                    [ [102.0, 2.0], [103.0, 3.0] ]
                ]
            }
EOF
                , 4978,
            ],
            [
                <<<EOF
            { 
                "type": "Polygon",
                "crs":{"type":"name","properties":{"name":"EPSG:4978"}},
                "coordinates": [
                    [ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0] ]
                ]
            }
EOF
                , 4978,
            ],
            [
                <<<EOF
            { 
                "type": "MultiPolygon",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [
                    [[[102.0, 2.0], [103.0, 2.0], [103.0, 3.0], [102.0, 3.0], [102.0, 2.0]]],
                    [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0]],
                    [[100.2, 0.2], [100.8, 0.2], [100.8, 0.8], [100.2, 0.8], [100.2, 0.2]]]
                ]
            }
EOF
                , 4326,
            ],
        ];
    }

    /**
     * @dataProvider getGeoJsonData
     */
    public function testFunction(string $geoJson, int $expected)
    {
        $em = $this->getEntityManager();
        $geom = new GeometryEntity();
        $geom->setGeom($geoJson);
        $em->persist($geom);
        $em->flush($geom);
        $class = GeometryEntity::class;
        $sql = <<<EOF
               SELECT ST_SRID(g.geom) FROM $class g WHERE g.id = :id
EOF;
        $query = $em->createQuery($sql);
        $query->setParameter('id', $geom->getId());
        $result = $query->getSingleScalarResult();
        $this->assertEquals($expected, $result);
    }
}
