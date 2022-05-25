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
	
	public static function getAddressObjectTable() : QueryModel
	{
		 return AddrObj::getInstance(DBFacade::getInstance());
	}

	public static function getAddressObjectParamsTable() : QueryModel
	{
			return AddrObjParams::getInstance(DBFacade::getInstance());
	}

	public static function getHousesTable() : QueryModel
	{
		return Houses::getInstance(DBFacade::getInstance());
	}

	public static function getAdminTable() : QueryModel
	{
		return AdminHierarchy::getInstance(DBFacade::getInstance());
	}

	public static function getMunTable() : QueryModel
	{
		return MunHierarchy::getInstance(DBFacade::getInstance());
	}

  public static function getObjectLevels() : QueryModel
  {
    return ObjLevels::getInstance(DBFacade::getInstance());
  }

  public static function getHousetype() : QueryModel
  {
    return Housetype::getInstance(DBFacade::getInstance());
  }

  public static function getAddhousetype() : QueryModel
  {
    return Addhousetype::getInstance(DBFacade::getInstance());
  }

  public static function getProductionDB() : QueryModel
  {
    return Production::getInstance(DBFacade::getInstance());
  }
}
