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

use Bnza\SPgSp\DBAL\Types\MultiPointType;
use Bnza\SPgSp\Tests\Fixtures\MultiPointEntity;

/**
 * Description of MultiPointTest
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class MultiPointTypeTest extends AbstractGeometryTypeTest {

    public function getGeometryTypeData(): array
    {
        return ['multipoint', MultiPointType::class, MultiPointEntity::class];
    }

    public function getGeoJsonData(): array
    {
        return [
            [
                <<<EOF
            { 
                "type": "MultiPoint",
                "crs":{"type":"name","properties":{"name":"EPSG:4326"}},
                "coordinates": [ [100.0, 0.0], [101.0, 1.0] ]
            }
EOF
            ]
        ];
    }

}
