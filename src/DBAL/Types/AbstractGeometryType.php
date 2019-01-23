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

namespace Bnza\SPgSp\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Description of AbstractGeometryType
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
abstract class AbstractGeometryType extends Type {

    /**
     * {@inheritDoc}
     */
    public function canRequireSQLConversion() {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform) {
        return sprintf('ST_GeomFromGeoJSON(%s)', $sqlExpr);
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValueSQL($sqlExpr, $platform) {
        return sprintf('ST_AsGeoJSON(%s, 7, 2)', $sqlExpr);
    }

    /**
     * {@inheritDoc}
     */
    public function getName() {
        return strtolower(substr(get_called_class(), strrpos(get_called_class(), "\\") + 1, -4));
    }

    /**
     * Adds a ORM/Column definition custom srid option
     * e.g Column(type="point", options={"srid":4326})
     *
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        $geomDeclArr = [$this->getName()];
        if (isset($fieldDeclaration["srid"])) {
            $geomDeclArr[] = $fieldDeclaration["srid"];
        }
        $geomDecl = implode(',', $geomDeclArr);
        return strtoupper("geometry($geomDecl)");
    }

}
