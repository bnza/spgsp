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

    /**
     * @return Connection
     * @throws UnsupportedPlatformException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected static function getConnection() {
        if (!isset(static::$connection)) {

            require_once 'connection_params.php';

            $connectionParams = array(
                'driver' => $GLOBALS['db_type'],
                'user' => $GLOBALS['test_connection_params']['username'],
                'password' => $GLOBALS['test_connection_params']['password'],
                'host' => $GLOBALS['db_host'],
                'port' => $GLOBALS['db_port']
            );


            static::$dbname = $GLOBALS['db_name'];
            $tmpConn = DriverManager::getConnection($connectionParams);
            $tmpConn->getSchemaManager()->dropAndCreateDatabase(static::$dbname);
            $tmpConn->close();

            $connectionParams['dbname'] = $GLOBALS['db_name'];

            static::$connection = DriverManager::getConnection($connectionParams);
        }
        return static::$connection;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager() {

        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxies');
        $config->setProxyNamespace('PBal\SMySp\Tests\Proxies');

        $config->setMetadataDriverImpl(
                $config->newDefaultAnnotationDriver(
                        array(realpath(__DIR__ . '/Fixtures')), true
                )
        );

        if (!isset($this->em)) {
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
//        static::$connection->getConfiguration()
//                ->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        $this->setUpFunctions();
    }

    public static function setUpBeforeClass() {
        static::$connection = static::getConnection();
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
        $this->getEntityManager()->clear();
    }

    public static function tearDownAfterClass() {
        //print 's';
        //var_dump(static::$connection->getSchemaManager()->listDatabases());
//        try {
//            static::$connection->getSchemaManager()->dropDatabase(static::$dbname);
//        } catch (\Doctrine\DBAL\Exception\DriverException $e) {
//            echo $e->getMessage();
//        }
    }

}
