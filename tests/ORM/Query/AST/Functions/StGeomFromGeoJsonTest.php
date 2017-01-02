<?php

/*
 * Copyright (C) 2017 Pietro Baldassarri <pietro.baldassarri@gmail.com>
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

namespace PBald\SPgSp\Tests\ORM\Query\AST\Functions;

use Doctrine\DBAL\Types\Type as DBALType;
use PBald\SPgSp\Tests\OrmTestCase;
use PBald\SPgSp\ORM\Query\AST\Functions\StGeomFromGeoJson;
use PBald\SPgSp\ORM\Query\AST\Functions\OpIsContainedBy;
use PBald\SPgSp\DBAL\Types\PointType;
use PBald\SPgSp\DBAL\Types\PolygonType;
use PBald\SPgSp\Tests\Fixtures\PointEntity;

/**
 * Description of StGeomFromGeoJsonTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class StGeomFromGeoJsonTest extends OrmTestCase {

    protected $points = [
        <<<EOF
            { 
                "type": "Point",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [2.0, 2.0] 
            }
EOF
        ,
        <<<EOF
            { 
                "type": "Point",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [5.0, 5.0] 
            }
EOF
    ];
    protected $box = <<<EOF
            {
                "type":"Polygon",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates":[[[1,1],[1,3],[3,3],[3,1],[1,1]]]
            }"
EOF;

    /**
     *  {@inheritDoc}
     */
    protected function setUp() {
        $this->setUpFunctions();
        $this->setUpTypeMapping();
        $this->createSchema(PointEntity::class);
        $this->loadEntities();
    }

    protected function loadEntities() {
        foreach ($this->points as $gj_point) {
            $point = new PointEntity();
            $point->setGeom($gj_point);
            $this->getEntityManager()->persist($point);
        }
        $this->getEntityManager()->flush();
    }

    protected function setUpTypeMapping() {
        if (!DBALType::hasType('point')) {
            DBALType::addType('point', PointType::class);
            $this->getEntityManager()
                    ->getConnection()
                    ->getDatabasePlatform()
                    ->registerDoctrineTypeMapping('point', 'point');
        }
    }

    protected function createSchema(string $entityClassName) {
        $classes[] = $this->getEntityManager()->getClassMetadata($entityClassName);
        $this->getSchemaTool()->createSchema($classes);
    }

    /**
     * Setup DQL functions
     */
    protected function setUpFunctions() {
        $configuration = $this->getEntityManager()->getConfiguration();
        $configuration->addCustomNumericFunction('OP_IsContainedBy', OpIsContainedBy::class);
        $configuration->addCustomNumericFunction('ST_GeomFromGeoJSON', StGeomFromGeoJson::class);
    }

    public function testFunction() {
        $em = $this->getEntityManager();
        $qc = $em->createQuery('SELECT COUNT(p.id) FROM PBald\SPgSp\Tests\Fixtures\PointEntity p');
        $count = $qc->getSingleScalarResult();
        $this->assertEquals(2, $count);

        $query = $em->createQuery(
                <<<EOF
               SELECT p FROM PBald\SPgSp\Tests\Fixtures\PointEntity p
               WHERE OP_IsContainedBy(p.geom,ST_GeomFromGeoJSON(:box)) = TRUE
EOF
        );

        $query->setParameter('box', $this->box);
        $result = $query->getResult();
        $this->assertEquals(1, count($result));
    }

}
