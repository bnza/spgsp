<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PBald\SPgSp\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Description of AbstractGeometryType
 *
 * @author Pietro Baldassarri <pietro.baldassarri@gmail.com>
 */
abstract class AbstractGeometryType extends Type {

    public function canRequireSQLConversion() {
        return true;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        //return $value ? json_encode($value) : $value;
        return $value;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform) {
        //return sprintf('ST_GeomFromGeoJSON(%s)', $sqlExpr);
        return sprintf('ST_SetSRID(ST_GeomFromGeoJSON(%s), 4326)', $sqlExpr);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) {
        //return $value ? json_decode($value) : $value;
        return $value;
    }

    public function convertToPHPValueSQL($sqlExpr, $platform) {
        return sprintf('ST_AsGeoJSON(%s)', $sqlExpr);
    }

    public function getName() {
        return strtolower(substr(get_called_class(), strrpos(get_called_class(), "\\") + 1, -4));
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
//        $type = strtoupper(substr(get_called_class(), strrpos(get_called_class(), "\\") + 1, -4));
//        return sprintf('GEOMETRY(%s, 4326)',$type);
        return strtoupper(substr(get_called_class(), strrpos(get_called_class(), "\\") + 1, -4));
    }

}
