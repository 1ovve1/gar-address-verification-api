<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;
use http\Exception\RuntimeException;

abstract class XMLFile
{
  private string $fileName = '';
  private ?string $region = null;
  private ?int $intRegion = null;
  private string $type;

  /**
   * @param string $fileName
   * @param string|null $region
   */
  public function __construct(string $fileName, ?string $region = null)
  {
    $this->fileName = $fileName;
    if (!is_null($region)) {
      $this->region = $region;
      $this->intRegion = (int) $region;
    }
  }

  public function __destruct()
  {
    static::getQueryModel()->save();
  }


  /**
   * @return string
   */
  public function getRegion(): string
  {
    if (is_null($this->region)) {
      throw new \RuntimeException("Try get the null region 
      (replace file to EveryRegion flooder if you wanna use regions)");
    }
    return $this->region;
  }

  /**
   * @return int
   */
  public function getIntRegion(): int
  {
    if (is_null($this->intRegion)) {
      throw new \RuntimeException("Try get the null region 
      (replace file to EveryRegion flooder if you wanna use regions)");
    }
    return $this->intRegion;
  }

  /**
   * @param string $region
   * @return XMLFile
   */
  public function setRegion(string $region): self
  {
    $this->region = $region;
    $this->intRegion = (int)$region;

    return $this;
  }

  /**
   * @return string
   */
  public function getFileName(): string
  {
    return $this->fileName;
  }

  /**
   * @return string
   */
  public function getPathToFile(): string
  {
    if (is_null($this->region)) {
      return $this->fileName;
    } else {
      return $this->region . '/' . $this->fileName;
    }
  }

  /**
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * @param string $type
   * @return XMLFile
   */
  public function bindType(string $type): self
  {
    $this->type = $type;
    return $this;
  }

  /**
   * return concrete table model that support current file
   * @return QueryModel
   */
  abstract static function getQueryModel(): QueryModel;

  function saveChangesInQueryModel(): void
  {
    $this::getQueryModel()->save();
  }

  /**
   * return elements of xml document
   * @return string elements names
   */
  abstract static function getElement(): string;

  /**
   * return attributes of elements in xml document
   * @return array attributes names
   */
  abstract static function getAttributes(): array;

  /**
   * procedure that contains main operations from exec method
   * @param array $values current parse element
   * @return void
   */
  abstract function execDoWork(array $values): void;
}