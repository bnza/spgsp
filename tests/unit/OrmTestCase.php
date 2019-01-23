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

namespace Bnza\SPgSp\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Description of OrmTestCase.
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class OrmTestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @return Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    protected static function getConnection(): Connection
    {
        if (!isset(self::$connection)) {
            $params = [
                'driver' => 'pdo_pgsql',
                'url' => getenv('DATABASE_URL')
            ];
            self::$connection = DriverManager::getConnection($params);
        }

        return self::$connection;
    }

    protected function beginTestTransaction()
    {

        $connection = $this->getEntityManager()->getConnection();
        $connection->beginTransaction();
        $connection->setAutoCommit(false);
    }

    protected function rollbackTestTransaction()
    {
        $connection = $this->getEntityManager()->getConnection();
        $connection->rollBack();
        $connection->setAutoCommit(true);
    }

    protected function getConfiguration(): Configuration
    {
        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setProxyDir(__DIR__.'/Proxies');
        $config->setProxyNamespace('Bnza\SMySp\Tests\Proxies');

        $config->setMetadataDriverImpl(
                $config->newDefaultAnnotationDriver(
                        array(realpath(__DIR__.'/Fixtures')), true
                )
        );

        return $config;
    }

    public function setUp()
    {
        $this->beginTestTransaction();
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getEntityManager(): EntityManager
    {
        if (!isset($this->em)) {
            $config = $this->getConfiguration();
            $this->em = EntityManager::create(static::getConnection(), $config);
        }

        return $this->em;
    }

    /**
     * @return SchemaTool
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getSchemaTool()
    {
        if (isset($this->schemaTool)) {
            return $this->schemaTool;
        }

        return new SchemaTool($this->getEntityManager());
    }

    /**
     * Teardown fixtures.
     * @throws \Exception $e
     */
    protected function tearDown()
    {
        try {
            $this->rollbackTestTransaction();
            $this->getEntityManager()->clear();
            $this->getEntityManager()->close();
        } catch (\Exception $e) {
            throw $e;
        }
        $this->em = null;
    }

    public static function tearDownAfterClass()
    {
        if (self::$connection) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
