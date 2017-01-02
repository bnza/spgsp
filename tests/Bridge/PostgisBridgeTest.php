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

namespace PBald\SPgSp\Tests\Bridge;

use PBald\SPgSp\Tests\OrmTestCase;
use PBald\SPgSp\Brigde\PostgisBridge;

/**
 * Description of PostgisBridgeTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class PostgisBridgeTest extends OrmTestCase {

    protected $postgis;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $this->postgis = new PostgisBridge(self::$connection);
    }

    public function testStSrid() {
        $gj = <<<EOF
            { 
                "type": "Point",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [100.0, 0.0] 
            }
EOF;
        $srid = $this->postgis->ST_SRID($gj);
        $this->assertEquals(4326, $srid);
    }

    public function testEmptyStSrid() {
        $gj = <<<EOF
            { 
                "type": "Point",
                "coordinates": [100.0, 0.0] 
            }
EOF;
        $srid = $this->postgis->ST_SRID($gj);
        $this->assertEquals(0, $srid);
    }

    public function testStSetSrid() {
        $gj = <<<EOF
            { 
                "type": "Point",
                "coordinates": [100.0, 0.0] 
            }
EOF;
        $this->postgis->ST_SetSRID($gj, 4326);
        $this->assertRegExp('/EPSG:4326/', $gj);
    }

    public function testStMulti() {
        $gj = <<<EOF
            { 
                "type": "Point",
                "coordinates": [100.0, 0.0] 
            }
EOF;
        $this->postgis->ST_Multi($gj);
        $this->assertRegExp('/MultiPoint/', $gj);
    }

    public function testStMakeBox2D() {
        $pll = <<<EOF
            { 
                "type": "Point",
                "coordinates": [0.0 , 0.0] 
            }
EOF;
        $pur = <<<EOF
            { 
                "type": "Point",
                "coordinates": [1.0, 1.0] 
            }
EOF;
        $box = $this->postgis->ST_MakeBox2D($pll, $pur);
        $this->assertRegExp('/Polygon/', $box);
        $box = $this->postgis->ST_MakeBox2D($pll, $pur, 4326);
        $this->assertRegExp('/EPSG:4326/', $box);
    }

}
