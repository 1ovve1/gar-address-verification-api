<?php declare(strict_types=1);

namespace GAR\Entity;

use GAR\Database\DBFacade;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\Models\{AddrObj,
  AddrObjParams,
  AdminHierarchy,
  Houses,
  MunHierarchy,
  ObjLevels,
  Housetype,
  Addhousetype,
  Production};

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
	public static function getAddressObjectTable() : QueryModel
	{
		 return new AddrObj(DBFacade::getInstance());
	}

  /**
   * Return addr_obj_params table
   * 
   * @return QueryModel
   */
	public static function getAddressObjectParamsTable() : QueryModel
	{
			return new AddrObjParams(DBFacade::getInstance());
	}

  /**
   * Return houses table
   * 
   * @return QueryModel
   */
	public static function getHousesTable() : QueryModel
	{
		return new Houses(DBFacade::getInstance());
	}

  /**
   * Return admin_hearachy table
   * 
   * @return QueryModel
   */
	public static function getAdminTable() : QueryModel
	{
		return new AdminHierarchy(DBFacade::getInstance());
	}

  /**
   * Return mun_hierarchy table
   * 
   * @return QueryModel
   */
	public static function getMunTable() : QueryModel
	{
		return new MunHierarchy(DBFacade::getInstance());
	}

  /**
   * Return obj_level table
   * 
   * @return QueryModel
   */
  public static function getObjectLevels() : QueryModel
  {
    return new ObjLevels(DBFacade::getInstance());
  }

  /**
   * Return housetype table
   * 
   * @return QueryModel
   */
  public static function getHousetype() : QueryModel
  {
    return new Housetype(DBFacade::getInstance());
  }

  /**
   * Return addhousetype table
   * 
   * @return QueryModel
   */
  public static function getAddhousetype() : QueryModel
  {
    return new Addhousetype(DBFacade::getInstance());
  }

  /**
   * Return prodaction table accsessor (not a model)
   * 
   * @return QueryModel
   */
  public static function getProductionDB() : QueryModel
  {
    return Production::getInstance(
      db: DBFacade::getInstance(), 
      createMetaTable: false
    );
  }
}
