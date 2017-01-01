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

use PBald\SPgSp\DBAL\Types\GeometryCollectionType;
use PBald\SPgSp\Tests\DBAL\Types\AbstractGeometryTypeTest;
use PBald\SPgSp\Tests\Fixtures\GeometryCollectionEntity;

/**
 * Description of MultiPointTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class GeometryCollectionTypeTest extends AbstractGeometryTypeTest {
    
    /**
     *  {@inheritDoc}
     */
    protected function setUpSpecificGeometry() {
        $this->doctrineType = $this->dbtype = 'geometrycollection';
        $this->geometryTypeClassName = GeometryCollectionType::class;
        $this->fixtureEntityClassName = GeometryCollectionEntity::class;
        $this->geojsons = array(
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
            );
    }

    public function testGeomsPersistence() {
        foreach ($this->getTestEntities() as $entity) {
            $this->_testGeomPersistence($entity);
        }
    }

}
