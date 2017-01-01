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

namespace PBald\SPgSp\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Description of OrmTestCase
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class OrmTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @var Connection
     */
    protected static $connection;

    /**
     *
     * @var string
     */
    protected static $dbname;

    /**
     * @var EntityManager
     */
    protected $em;

    protected static function getConnectionParams(bool $db_name = false) {
        static $connectionParams;
        static $connectionParamsDB;

        if (!isset($connectionParams)) {
            require_once 'connection_params.php';

            $connectionParams = [
                'driver' => $GLOBALS['db_type'],
                'user' => $GLOBALS['test_connection_params']['username'],
                'password' => $GLOBALS['test_connection_params']['password'],
                'host' => $GLOBALS['db_host'],
                'port' => $GLOBALS['db_port']
            ];

            static::$dbname = $GLOBALS['db_name'];
            $connectionParamsDB = $connectionParams;
            $connectionParamsDB['dbname'] = self::$dbname;
        }
        return $db_name ? $connectionParamsDB : $connectionParams;
    }

    protected static function getConnection() {
        if (!isset(self::$connection)) {
            self::$connection = DriverManager::getConnection(self::getConnectionParams(true));
        }
        return self::$connection;
    }

    /**
     * @return Connection
     * @throws UnsupportedPlatformException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected static function createSpatialSchema() {
        $tmpConn = DriverManager::getConnection(self::getConnectionParams());
        $tmpConn->getSchemaManager()->dropAndCreateDatabase(self::$dbname);
        $tmpConn->close();
        self::getConnection()->exec('CREATE EXTENSION postgis');
    }

    protected function getConfiguration() {
        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('PBal\SMySp\Tests\Proxies');

        $config->setMetadataDriverImpl(
                $config->newDefaultAnnotationDriver(
                        array(realpath(__DIR__ . '/Fixtures')), true
                )
        );
        return $config;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager() {

        if (!isset($this->em)) {
            $config = $this->getConfiguration();
            $this->em = EntityManager::create(static::getConnection(), $config);
        }
        return $this->em;
    }

    /**
     * @return SchemaTool
     */
    protected function getSchemaTool() {
        if (isset($this->schemaTool)) {
            return $this->schemaTool;
        }
        return new SchemaTool($this->getEntityManager());
    }

    protected function setUp() {
        $this->setUpFunctions();
    }

    public static function setUpBeforeClass() {
        static::createSpatialSchema();
    }

    /**
     * Setup DQL functions
     */
    protected function setUpFunctions() {
        $configuration = $this->getEntityManager()->getConfiguration();
        $configuration->addCustomStringFunction('GeomFromText', 'PBald\SPgSp\ORM\Query\AST\Functions\GeomFromText');
    }

    /**
     * Teardown fixtures
     */
    protected function tearDown() {
        parent::tearDown();
        $this->getEntityManager()->clear();
        $this->getEntityManager()->close();
        $this->em = null;
    }

    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
        self::$connection->close();
        self::$connection = null;
    }

}
