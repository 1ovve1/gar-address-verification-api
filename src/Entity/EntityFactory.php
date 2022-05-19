<?php declare(strict_types=1);

namespace GAR\Entity;

use GAR\Database\DBFacade;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\Models\AddrObj;
use GAR\Entity\Models\AddrObjParams;
use GAR\Entity\Models\AdminHierarchy;
use GAR\Entity\Models\Houses;
use GAR\Entity\Models\MunHierarchy;
use GAR\Entity\Models\ObjLevels;

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
		 return new AddrObj(DBFacade::getInstance());
	}

	public static function getAddressObjectParamsTable() : QueryModel
	{
			return new AddrObjParams(DBFacade::getInstance());
	}

	public static function getHousesTable() : QueryModel
	{
		return new Houses(DBFacade::getInstance());
	}

	public static function getAdminTable() : QueryModel
	{
		return new AdminHierarchy(DBFacade::getInstance());
	}

	public static function getMunTable() : QueryModel
	{
		return new MunHierarchy(DBFacade::getInstance());
	}

  public static function getObjectLevels() : QueryModel
  {
    return new ObjLevels(DBFacade::getInstance());
  }
}
