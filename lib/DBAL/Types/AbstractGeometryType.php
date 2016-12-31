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
