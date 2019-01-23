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

/**
 * Description of MultiPointTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */

namespace Bnza\Tests\DBAL\Types;

use Bnza\SPgSp\Tests\OrmTestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;

class SchemaTest extends OrmTestCase {

    public function testOrmTestCaseSetUp() {
        $this->assertInstanceOf(Connection::class, self::getConnection());
        $this->assertInstanceOf(EntityManager::class, $this->getEntityManager());
    }

}
