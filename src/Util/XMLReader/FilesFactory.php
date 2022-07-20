<?php declare(strict_types=1);

namespace GAR\Util\XMLReader;


use GAR\Util\XMLReader\Files\{AsAddhousetype,
  AsAddressObject,
  AsAddressObjectParams,
  AsAdminHierarchy,
  AsHouses,
  AsHousetype,
  AsMunHierarchy,
  AsObjectLevels};
use GAR\Util\XMLReader\Reader\ConcreteReader;



class FilesFactory
{
  const FILES = [
    'AS_HOUSES' => 'AS_HOUSES',
    'ADDR_OBJ' => 'AS_ADDR_OBJ',
    'ADMIN_HIERARCHI' => 'AS_ADM_HIERARCHY',
    'MUN_HIERARCHI' => 'AS_MUN_HIERARCHY',
    'ADDR_OBJ_PARAMS' => 'AS_ADDR_OBJ_PARAMS',
    'OBJECT_LEVELS' => 'AS_OBJECT_LEVELS',
    'HOUSETYPE' => 'AS_HOUSE_TYPES',
    'ADDHOUSETYPE' => 'AS_ADDHOUSE_TYPES',
  ];

	const regions = [
		'1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
		'11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
		'21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
		'31', '32', '33', '34', '35', '36', '37', '38', '39', '40',
		'41', '42', '43', '44', '45', '46', '47', '48', '49', '50',
		'51', '52', '53', '54', '55', '56', '57', '58', '59', '60',
		'61', '62', '63', '64', '65', '66', '67', '68', '69', '70',
		'71', '72', '73', '74', '75', '76', '77', '78', '79', '80',
		'81', '82', '83', '84', '85', '86', '87', '88', '89', '91',
		'92', '99',

	];

  public static function execAddrObj() : ConcreteReader
	{
		return self::prepare(new AsAddressObject(), self::FILES['ADDR_OBJ']);

	}

	public static function execAddressObjParams() : ConcreteReader
	{
		return self::prepare(new AsAddressObjectParams(), self::FILES['ADDR_OBJ_PARAMS']);

	}

	public static function execHouses() : ConcreteReader
	{
		return self::prepare(new AsHouses(), self::FILES['AS_HOUSES']);

	}

	public static function execAdminHierarchi() : ConcreteReader
	{
		return self::prepare(new AsAdminHierarchy(), self::FILES['ADMIN_HIERARCHI']);

	}

	public static function execMunHierachi() : ConcreteReader
	{
		return self::prepare(new AsMunHierarchy(), self::FILES['MUN_HIERARCHI']);
	}

  public static function execObjectLevels() : ConcreteReader
  {
    return new AsObjectLevels(self::FILES['OBJECT_LEVELS']);
  }

  public static function execHousetype() : ConcreteReader
  {
    return new AsHousetype(self::FILES['HOUSETYPE']);
  }

  public static function execAddhousetype() : ConcreteReader
  {
    return new AsAddhousetype(self::FILES['ADDHOUSETYPE']);
  }

	private static function prepare(ConcreteReader $reader, string $file) : ConcreteReader
	{

    foreach (self::regions as $value) {
			$path = $value . '/' . $file;
			$reader->linked($path);
		}

		return $reader;
	}
}
