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

namespace PBald\SMySp\Tests\DBAL\Types;

use GeoPHP\GeoPhp;
use PBald\SMySp\DBAL\Types\MultiLineStringType;
use PBald\SMySp\Tests\DBAL\Types\AbstractGeometryTypeTest;
use PBald\SMySp\Tests\Fixtures\MultiLineStringEntity;

/**
 * Description of MultiPointTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class MultiLineStringTypeTest extends AbstractGeometryTypeTest {

    /**
     *  {@inheritDoc}
     */
    protected function setUpSpecificGeometry() {
        $this->doctrineType = $this->dbtype = 'multilinestring';
        $this->geometryTypeClassName = MultiLineStringType::class;
        $this->fixtureEntityClassName = MultiLineStringEntity::class;
    }

    protected function getTestEntities() {
        $entities = array();
        $wkts = array(
            'MULTILINESTRING ((3 4, 10 50, 20 25), (-5 -8, -10 -8, -15 -4))',
        );
        foreach ($wkts as $wkt) {
            $geom = GeoPhp::load($wkt, 'wkt');
            $entity = new MultiLineStringEntity();
            $entity->setGeom($geom);
            array_push($entities, $entity);
        }
        return $entities;
    }

    public function testGeomsPersistence() {
        foreach ($this->getTestEntities() as $entity) {
            $this->_testGeomPersistence($entity);
        }
    }

}
