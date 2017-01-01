<?php

/*
 * Copyright (C) 2016 Pietro Baldassarri <pietro.baldassarri@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PBald\SPgSp\Tests\DBAL\Types;

use PBald\SPgSp\DBAL\Types\GeometryType;
use PBald\SPgSp\Tests\DBAL\Types\AbstractGeometryTypeTest;
use PBald\SPgSp\Tests\Fixtures\GeometryEntity;

/**
 * Description of MultiPointTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class GeometryTypeTest extends AbstractGeometryTypeTest {

    protected $geojsons = [
        <<<EOF
            { 
                "type": "Point",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [100.0, 0.0] 
            }
EOF
        ,
        <<<EOF
            { 
                "type": "MultiPoint",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
        ,
        <<<EOF
            { 
                "type": "LineString",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
        ,
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
        ,
        <<<EOF
            { 
                "type": "Polygon",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [
                    [ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0] ]
                ]
            }
EOF
        ,
        <<<EOF
            { 
                "type": "Polygon",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [
                    [ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0] ],
                    [ [100.2, 0.2], [100.8, 0.2], [100.8, 0.8], [100.2, 0.8], [100.2, 0.2] ]
                ]
            }
EOF
        ,
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
        ,
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
    ];

    /**
     *  {@inheritDoc}
     */
    protected function setUpSpecificGeometry() {
        $this->doctrineType = $this->dbtype = 'geometry';
        $this->geometryTypeClassName = GeometryType::class;
        $this->fixtureEntityClassName = GeometryEntity::class;
    }

    public function testGeomsPersistence() {
        foreach ($this->getTestEntities() as $entity) {
            $this->_testGeomPersistence($entity);
        }
    }

}
