<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;

abstract class XMLFile
{
  private string $fileName = '';

  /**
   * @param string $fileName
   */
  public function __construct(string $fileName)
  {
    $this->fileName = $fileName;
  }

  /**
   * @return string
   */
  public function getFileName(): string
  {
    return $this->fileName;
  }

  /**
   * return elements of xml document
   * @return array elements names
   */
  abstract static function getElements(): array;

  /**
   * return attributes of elements in xml document
   * @return array attributes names
   */
  abstract static function getAttributes(): array;

  /**
   * procedure that contains main operations from exec method
   * @param QueryModel $model table model
   * @param array $values current parse element
   * @return void
   */
  abstract function execDoWork(QueryModel $model, array $values);
}