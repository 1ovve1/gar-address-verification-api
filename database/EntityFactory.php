<?php

declare(strict_types=1);

namespace DB;

use DB\ORM\DBFacade;
use DB\ORM\Table\SQL\QueryModel;
use DB\Models\{
    Addhousetype,
    AddrObj,
    AddrObjParams,
    Houses,
    Housetype,
    MunHierarchy,
    ObjLevels,
    Database
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
        return AddrObj::getInstance(DBFacade::getInstance());
    }

    /**
     * Return addr_obj_params table
     *
     * @return QueryModel
     */
    public static function getAddressObjectParamsTable(): QueryModel
    {
		return AddrObjParams::getInstance(DBFacade::getInstance());
    }

    /**
     * Return houses table
     *
     * @return QueryModel
     */
    public static function getHousesTable(): QueryModel
    {
		return Houses::getInstance(DBFacade::getInstance());
    }

    /**
     * Return mun_hierarchy table
     *
     * @return QueryModel
     */
    public static function getMunTable(): QueryModel
    {
		return MunHierarchy::getInstance(DBFacade::getInstance());
    }

    /**
     * Return obj_level table
     *
     * @return QueryModel
     */
    public static function getObjectLevels(): QueryModel
    {
		return ObjLevels::getInstance(DBFacade::getInstance());
    }

    /**
     * Return housetype table
     *
     * @return QueryModel
     */
    public static function getHousetype(): QueryModel
    {
		return Housetype::getInstance(DBFacade::getInstance());
    }

    /**
     * Return addhousetype table
     *
     * @return QueryModel
     */
    public static function getAddhousetype(): QueryModel
    {
		return Addhousetype::getInstance(DBFacade::getInstance());
    }

    /**
     * Return prodaction table accsessor (not a model)
     *
     * @return QueryModel
     */
    public static function getProductionDB(): QueryModel
    {
        return Database::getInstance(
            db: DBFacade::getInstance(),
            createMetaTable: false
        );
    }
}
