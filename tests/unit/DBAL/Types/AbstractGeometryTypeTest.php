<?php

/*
 * Copyright (C) 2019 Pietro Baldassarri <pietro.baldassarri@gmail.com>
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

namespace Bnza\SPgSp\Tests\DBAL\Types;

use Bnza\SPgSp\Tests\OrmTestCase;
use Doctrine\DBAL\Types\Type as DBALType;

/**
 * Description of AbstractGeometryTypeTest.
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
abstract class AbstractGeometryTypeTest extends OrmTestCase
{
    /**
     * Used by Doctrine\DBAL\Platforms\AbstractPlatform::registerDoctrineTypeMapping
     * Should be initialized during the setUp function setUpSpecificGeometry.
     *
     * @var string
     */
    protected $dbtype;

    /**
     * Used by Doctrine\DBAL\Platforms\AbstractPlatform::registerDoctrineTypeMapping
     * Should be initialized during the setUp function setUpSpecificGeometry.
     *
     * @var string
     */
    protected $doctrineType;

    /**
     * Doctrine\DBAL\Types\Type
     * Should be initialized during the setUp function setUpSpecificGeometry.
     *
     * @var string
     */
    protected $geometryTypeClassName;

    /**
     * Used by Doctrine\ORM\EntityManager::getClassMetadata
     * Should be initialized during the setUp function setUpSpecificGeometry.
     *
     * @var string
     */
    protected $fixtureEntityClassName;

    /**
     * geoJSON string array.
     *
     * @var string[]
     */
    protected $geojsons;

    /**
     * Data provider for testGeometryIsPersistedToDb
     */
    abstract protected function getGeoJsonData(): array;

    /**
     * Return the setUpGeometryType() arguments array
     */
    abstract protected function getGeometryTypeData(): array;

    protected function setUpGeometryType(string $type, string $typeClass, string $entityClass)
    {
        $this->doctrineType = $this->dbtype = $type;
        $this->fixtureEntityClassName = $entityClass;
        $this->geometryTypeClassName = $typeClass;

        if(!DBALType::hasType($this->doctrineType)) {
            DBALType::addType($this->doctrineType, $this->geometryTypeClassName);

            $this->getEntityManager()
                ->getConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping($this->dbtype, $this->doctrineType);
        }
    }

    protected function createTestTable()
    {
        $classes[] = $this->getEntityManager()->getClassMetadata($this->fixtureEntityClassName);
        $this->getSchemaTool()->createSchema($classes);
    }

    protected function geomTableExists(): bool
    {
        $table_name = $this->doctrineType."_test_table";
        $sql = 'SELECT COUNT(*) FROM information_schema.tables where table_name = :name';
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute(['name' => $table_name]);
        $count = $stmt->fetchColumn();
        return (bool) $count;
    }

    public function assertPreConditions()
    {
        $this->assertTrue($this->geomTableExists());
    }

    /**
     * @dataProvider getGeoJsonData
     * @param string $geoJson
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGeometryIsPersistedToDb(string $geoJson)
    {
        $entity = $this->getTestEntity($geoJson);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()
                ->getRepository($this->fixtureEntityClassName)
                ->find($id);

        $this->assertJsonStringEqualsJsonString(
                \json_encode($entity),
                \json_encode($queryEntity));
    }

    protected function getTestEntity(string $geoJson)
    {
        $entity = new $this->fixtureEntityClassName();
        $entity->setGeom($geoJson);
        return $entity;
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpGeometryType(...$this->getGeometryTypeData());
        $this->createTestTable();
    }
}
