<?php

namespace Bnza\SPgSp\Tests\ORM\Query\AST\Functions;

use Bnza\SPgSp\DBAL\Types\GeometryType;
use Bnza\SPgSp\Tests\Fixtures\GeometryEntity;
use Bnza\SPgSp\Tests\OrmTestCase;
use Bnza\SPgSp\ORM\Query\AST\Functions\StGeomFromGeoJson;
use Doctrine\DBAL\Types\Type as DBALType;

abstract class AbstractFunctionTest extends OrmTestCase
{
    abstract protected function getASTFunctions(): array;

    /**
     * Setup DQL functions.
     */
    protected function setUpFunctions()
    {
        $configuration = $this->getEntityManager()->getConfiguration();
        foreach ($this->getASTFunctions() as $name => $value) {
            $method = sprintf('addCustom%sFunction', ucfirst($value['type']));
            $configuration->$method($name, $value['class']);
            //$configuration->addCustomStringFunction('ST_GeomFromGeoJSON', StGeomFromGeoJson::class);
        }
    }

    protected function createTestTable()
    {
        $classes[] = $this->getEntityManager()->getClassMetadata(GeometryEntity::class);
        $this->getSchemaTool()->createSchema($classes);
    }

    protected function setUpGeometryType()
    {
        if (!DBALType::hasType('geometry')) {
            DBALType::addType('geometry', GeometryType::class);

            $this->getEntityManager()
                ->getConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('geometry', 'geometry');
        }
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpFunctions();
        $this->setUpGeometryType();
        $this->createTestTable();
    }
}
