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

namespace PBald\SPgSp\Brigde;

use PDO;
use InvalidArgumentException;
use Doctrine\DBAL\Connection;

/**
 * Description of PostgisBridge
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
class PostgisBridge {

    public function __construct(Connection $dbalConnection) {
        $this->connection = $dbalConnection;
    }

    /**
     * @see http://postgis.net/docs/ST_Multi.html
     * @staticvar Doctrine\DBAL\Statement $stmt
     * @param string $geom
     */
    public function ST_MakeBox2D(string $pointLowLeft, string $pointUpRight, int $srid = 0) {
        static $stmt;

        if (is_null($stmt)) {
            $q = <<<EOL
                    SELECT ST_AsGeoJSON(
                        ST_SetSRID(
                            ST_MakeBox2D(
                                ST_GeomFromGeoJSON(:pll),
                                ST_GeomFromGeoJSON(:pur)
                            )
                            , :srid)
                        ,7,2
                    )
EOL;
            $stmt = $this->connection->prepare($q);
        }
        $stmt->execute(['pll' => $pointLowLeft, 'pur' => $pointUpRight, 'srid' => $srid]);
        return $stmt->fetchColumn();
    }

    /**
     * @see http://postgis.net/docs/ST_Multi.html
     * @staticvar Doctrine\DBAL\Statement $stmt
     * @param string $geom
     */
    public function ST_Multi(string &$geom) {
        static $stmt;

        if (is_null($stmt)) {
            $stmt = $this->connection->prepare("SELECT ST_AsGeoJSON(ST_Multi(ST_GeomFromGeoJSON(:geom)),7,2)");
        }
        $stmt->execute(['geom' => $geom]);
        $geom = $stmt->fetchColumn();
    }

    /**
     * @see http://postgis.net/docs/ST_SetSRID.html
     * @staticvar Doctrine\DBAL\Statement $stmt
     * @param string $geom
     */
    public function ST_SetSRID(string &$geom, int $srid) {
        static $stmt;

        if (is_null($stmt)) {
            $stmt = $this->connection->prepare("SELECT ST_AsGeoJSON(ST_SetSRID(ST_GeomFromGeoJSON(:geom),:srid),7,2)");
        }
        $stmt->execute(['geom' => $geom, 'srid' => $srid]);
        $geom = $stmt->fetchColumn();
    }

    /**
     * @see http://postgis.net/docs/ST_SRID.html
     * @staticvar Doctrine\DBAL\Statement $stmt
     * @param string $geom
     * @return integer
     */
    public function ST_SRID(string $geom) {
        static $stmt;

        if (is_null($stmt)) {
            $stmt = $this->connection->prepare("SELECT ST_SRID(ST_GeomFromGeoJSON(:geom))");
        }
        $stmt->execute(['geom' => $geom]);
        return $stmt->fetchColumn();
    }

}
