<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

// configure order of uploading files

use _PHPStan_9a6ded56a\Nette\Neon\Exception;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\ArrayShape;

enum ConfigList
{
  // case AsHouseTypes;
  // case AsAddhouseTypes;
  case AsObjectLevels;
  case AsAddrObj;
  // case AsAddrObjParams;
  // case AsHouses;
  // case AsMunHierarchy;

  const EVERY_REGION_FLOODER = "\\EveryRegion\\";
  const SINGLE_FLOODER = "\\Single\\";
  const EVERY_REGION_KEY = 'every_region';
  const SINGLE_KEY = 'single';

  static function getRealFileNameFromEnum(ConfigList $elem) : string
  {
    $realName = '';
    $factName = $elem->name;

    foreach(str_split($factName) as $pos => $char) {
      if (ctype_upper($char) && $pos !== 0) {
        $realName .= '_';
      }
      $realName .= strtoupper($char);
    }

    return $realName;
  }

  #[ArrayShape([self::EVERY_REGION_KEY => "string", self::SINGLE_KEY => "string"])]
  public static function getNamespaceFromEnum(ConfigList $elem) : array
  {
    $defaultNamespace = "\\" . __NAMESPACE__;
    $tryEveryRegionFlooder = $defaultNamespace . self::EVERY_REGION_FLOODER . $elem->name;
    $trySingleFlooder = $defaultNamespace . self::SINGLE_FLOODER . $elem->name;

    if (class_exists($tryEveryRegionFlooder)) {
      $namespace = [self::EVERY_REGION_KEY => $tryEveryRegionFlooder];
    } else if (class_exists($trySingleFlooder)) {
      $namespace = [self::SINGLE_KEY => $trySingleFlooder];
    } else {
      throw new \RuntimeException("Class {$elem->name} not found");
    }

    return $namespace;
  }
}