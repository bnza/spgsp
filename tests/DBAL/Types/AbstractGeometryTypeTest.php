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

use GeoPHP\GeoPhp;
use PBald\SPgSp\Tests\OrmTestCase;
use Doctrine\DBAL\Types\Type as DBALType;

/**
 * Description of AbstractGeometryTypeTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
abstract class AbstractGeometryTypeTest extends OrmTestCase {

    /**
     * Used by Doctrine\DBAL\Platforms\AbstractPlatform::registerDoctrineTypeMapping
     * Should be initialized during the setUp function setUpSpecificGeometry
     * 
     * @var string 
     */
    protected $dbtype;

    /**
     * Used by Doctrine\DBAL\Platforms\AbstractPlatform::registerDoctrineTypeMapping
     * Should be initialized during the setUp function setUpSpecificGeometry
     * 
     * @var string 
     */
    protected $doctrineType;

    /**
     * Doctrine\DBAL\Types\Type
     * Should be initialized during the setUp function setUpSpecificGeometry
     * 
     * @var string 
     */
    protected $geometryTypeClassName;

    /**
     * Used by Doctrine\ORM\EntityManager::getClassMetadata
     * Should be initialized during the setUp function setUpSpecificGeometry
     * 
     * @var string 
     */
    protected $fixtureEntityClassName;

    /**
     * WKT string array
     * 
     * @var string[]
     */
    protected $wkts;
    
    /**
     * Function set up set up $dbtype, $doctrineType, $geometryTypeClassName,
     * $fixtureEntityClassName and $wkts.
     */
    abstract protected function setUpSpecificGeometry();

    protected function setUp() {

        $this->setUpSpecificGeometry();

        DBALType::addType($this->doctrineType, $this->geometryTypeClassName);

        parent::setUp();

        $classes[] = $this->getEntityManager()->getClassMetadata($this->fixtureEntityClassName);
        $this->getSchemaTool()->createSchema($classes);

        $this->getEntityManager()
                ->getConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping($this->dbtype, $this->doctrineType);
    }

    /**
     * Function accepts a geometry *Entity instance
     * @param type $entity
     */
    public function _testGeomPersistence($entity) {

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()
                ->getRepository($this->fixtureEntityClassName)
                ->find($id);

        $this->assertEquals($entity, $queryEntity);
    }
    
    /**
     * Function return an array of geometry *Entity instances with correctly 
     * filled geom property using the $this->wkts array
     *  
     * @return array
     */
    protected function getTestEntities() {
        $entities = array();
        foreach ($this->wkts as $wkt) {
            $geom = GeoPhp::load($wkt, 'wkt');
            $entity = new $this->fixtureEntityClassName();
            $entity->setGeom($geom);
            array_push($entities, $entity);
        }
        return $entities;
    }

}
