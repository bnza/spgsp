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

use PBald\SMySp\DBAL\Types\PolygonType;
use PBald\SMySp\Tests\DBAL\Types\AbstractGeometryTypeTest;
use PBald\SMySp\Tests\Fixtures\PolygonEntity;

/**
 * Description of MultiPointTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class PolygonStringTypeTest extends AbstractGeometryTypeTest {

    /**
     *  {@inheritDoc}
     */
    protected function setUpSpecificGeometry() {
        $this->doctrineType = $this->dbtype = 'polygon';
        $this->geometryTypeClassName = PolygonType::class;
        $this->fixtureEntityClassName = PolygonEntity::class;
        $this->wkts = array(
            'POLYGON ((1 1, 5 1, 5 5, 1 5, 1 1), (2 2, 3 2, 3 3, 2 3, 2 2))',
        );
    }

    public function testGeomsPersistence() {
        foreach ($this->getTestEntities() as $entity) {
            $this->_testGeomPersistence($entity);
        }
    }

}
