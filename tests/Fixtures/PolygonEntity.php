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

namespace PBald\SPgSp\Tests\Fixtures;

use GeoPHP\Geometry\Polygon;

/**
 * Description of Polygon
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 * 
 * @Entity
 * @Table(name="testPolygon")
 */
class PolygonEntity {
    /**
     * @var int $id
     *
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    protected $id;

    /**
     * @var Polygon $geom
     *
     * @Column(type="polygon", nullable=true)
     */
    protected $geom;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set geom
     *
     * @param string $geom
     *
     * @return self
     */
    public function setGeom(string $geom)
    {
        $this->geom = $geom;

        return $this;
    }

    /**
     * Get polygon
     *
     * @return Polygon
     */
    public function getGeom()
    {
        return $this->geom;
    }
}
