<?php

declare(strict_types=1);

namespace GAR\Entity;

use GAR\Database\DBFacade;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\Models\{
    Addhousetype,
    AddrObj,
    AddrObjParams,
    AdminHierarchy,
    Houses,
    Housetype,
    MunHierarchy,
    ObjLevels,
    Production
};

/**
 * BD FACTORY CLASS
 *
 * FULL-STATIC FABRIC
 * RETURN COMPLETED MODELS
 */
class EntityFactory
{
    /**
   * Return addr_obj table
   *
   * @return QueryModel
   */
    public static function getAddressObjectTable(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new AddrObj(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return addr_obj_params table
     *
     * @return QueryModel
     */
    public static function getAddressObjectParamsTable(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new AddrObjParams(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return houses table
     *
     * @return QueryModel
     */
    public static function getHousesTable(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new Houses(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return mun_hierarchy table
     *
     * @return QueryModel
     */
    public static function getMunTable(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new MunHierarchy(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return obj_level table
     *
     * @return QueryModel
     */
    public static function getObjectLevels(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new ObjLevels(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return housetype table
     *
     * @return QueryModel
     */
    public static function getHousetype(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new Housetype(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return addhousetype table
     *
     * @return QueryModel
     */
    public static function getAddhousetype(): QueryModel
    {
        static $model = null;
        if (null === $model) {
            $model = new Addhousetype(DBFacade::getInstance());
        }
        return $model;
    }

    /**
     * Return prodaction table accsessor (not a model)
     *
     * @return QueryModel
     */
    public static function getProductionDB(): QueryModel
    {
        return Production::getInstance(
            db: DBFacade::getInstance(),
            createMetaTable: false
        );
    }
}
