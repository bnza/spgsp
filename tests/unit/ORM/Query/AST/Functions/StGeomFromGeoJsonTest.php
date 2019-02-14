<?php

namespace Bnza\SPgSp\Tests\ORM\Query\AST\Functions;

use Bnza\SPgSp\Tests\Fixtures\GeometryEntity;
use Bnza\SPgSp\ORM\Query\AST\Functions\StGeomFromGeoJson;

class StGeomFromGeoJsonTest extends AbstractFunctionTest
{
    protected function getASTFunctions(): array
    {
        return [
            'ST_GeomFromGeoJSON' => [
                'class' => StGeomFromGeoJson::class,
                'type' => 'string',
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
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [100.0, 0.0] 
            }
EOF
            ],
            [
                <<<EOF
            { 
                "type": "MultiPoint",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
            ],
            [
                <<<EOF
            { 
                "type": "LineString",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
            ],
            [
                <<<EOF
            { 
                "type": "MultiLineString",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [
                    [ [100.0, 0.0], [101.0, 1.0] ],
                    [ [102.0, 2.0], [103.0, 3.0] ]
                ]
            }
EOF
            ],
            [
                <<<EOF
            { 
                "type": "Polygon",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [
                    [ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0] ]
                ]
            }
EOF
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
            ],
            [
                <<<EOF
            { 
                "type": "GeometryCollection",
                "geometries": [
                    { 
                        "type": "Point",
                        "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                        "coordinates": [100.0, 0.0]
                    },
                    { 
                        "type": "LineString",
                        "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                        "coordinates": [ [101.0, 0.0], [102.0, 1.0] ]
                    }
                ]
            }
EOF
            ],
        ];
    }

    /**
     * @dataProvider getGeoJsonData
     */
    public function testFunction(string $geoJson)
    {
        $em = $this->getEntityManager();
        $geom = new GeometryEntity();
        $geom->setGeom($geoJson);
        $em->persist($geom);
        $em->flush($geom);
        $class = GeometryEntity::class;
        $sql = <<<EOF
               SELECT ST_GeomFromGeoJSON(:gj) FROM $class g
EOF;
        $query = $em->createQuery($sql);
        $query->setParameter('gj', $geoJson);
        $result = $query->getSingleScalarResult();
        $this->assertTrue(\ctype_xdigit($result));
    }
}
